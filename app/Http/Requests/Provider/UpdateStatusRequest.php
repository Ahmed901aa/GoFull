<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:en_route,arrived,in_progress,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be one of: en_route, arrived, in_progress, completed.',
        ];
    }

    /**
     * The order lifecycle, in order. Progress may only move FORWARD along
     * it. `pending` and `cancelled` are deliberately absent: an order that
     * has not been accepted, or that was cancelled, accepts no progress
     * updates at all.
     */
    public const LIFECYCLE = ['accepted', 'en_route', 'arrived', 'in_progress', 'completed'];

    /**
     * Validate the status transition.
     *
     * Rule: FORWARD-ONLY, skipping allowed.
     *
     * Strict step-by-step adjacency was tried first and is wrong for a
     * mobile client: the intermediate markers (en_route / arrived /
     * in_progress) are emitted automatically as the provider moves between
     * screens, so a single dropped request left the order one step behind
     * and permanently rejected every later update — the provider could
     * never finish the job ("Failed to update status" on Confirm Receipt).
     *
     * Forward-only keeps every protection that actually matters:
     *   - no going backwards (completed → accepted, etc.)
     *   - no resurrecting a cancelled order (not in LIFECYCLE)
     *   - no updating an order that was never accepted
     *   - no replay resetting timestamps (same-status is a no-op)
     * The controller backfills timestamps for any skipped step so the
     * audit trail stays complete.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Route parameter is {serviceRequest} (see routes/api.php).
            $request = $this->route('serviceRequest');

            // Resolve model if route binding gave us a raw ID
            if (is_string($request) || is_int($request)) {
                $request = \App\Models\ServiceRequest::find($request);
            }

            if (! $request) return;

            $new = $this->input('status');

            // Re-sending the current status is treated as an idempotent
            // no-op by the controller (safe retry) — not a violation.
            if ($request->status === $new) return;

            $currentIndex = array_search($request->status, self::LIFECYCLE, true);
            $newIndex     = array_search($new, self::LIFECYCLE, true);

            // Not on the lifecycle at all → pending (never accepted) or
            // cancelled (terminal). Neither can be progressed.
            if ($currentIndex === false) {
                $validator->errors()->add(
                    'status',
                    "An order with status '{$request->status}' can no longer be updated."
                );
                return;
            }

            if ($newIndex === false || $newIndex < $currentIndex) {
                $validator->errors()->add(
                    'status',
                    "Cannot move an order from '{$request->status}' back to '{$new}'."
                );
            }
        });
    }
}