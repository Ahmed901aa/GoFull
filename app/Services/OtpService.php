<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const OTP_EXPIRY_MINUTES = 5;

    // ─── إرسال OTP ────────────────────────────────────────────────
    public static function send(string $phone, string $purpose = 'registration'): array
    {
        // منع الـ spam — لو في OTP لسه ما انتهى ما نبعث ثاني
        $existing = Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            $wait = now()->diffInSeconds($existing->expires_at);
            return [
                'success' => false,
                'message' => "انتظر {$wait} ثانية قبل طلب رمز جديد.",
            ];
        }

        // إلغاء كل الـ OTPs القديمة لهذا الرقم
        Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->update(['is_used' => true]);

        // توليد رمز عشوائي 6 أرقام
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // حفظ في DB
        Otp::create([
            'phone'      => $phone,
            'code'       => $code,
            'purpose'    => $purpose,
            'is_used'    => false,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        // إرسال SMS
        $sent = self::sendSms($phone, $code);

        if (! $sent) {
            return [
                'success' => false,
                'message' => 'فشل إرسال الرمز، يرجى المحاولة مرة أخرى.',
            ];
        }

        return [
            'success' => true,
            'message' => "تم إرسال رمز التحقق إلى {$phone}. صالح لمدة " . self::OTP_EXPIRY_MINUTES . " دقائق.",
        ];
    }

    // ─── التحقق من OTP ────────────────────────────────────────────
    public static function verify(string $phone, string $code, string $purpose = 'registration'): array
    {
        $otp = Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (! $otp) {
            return ['success' => false, 'message' => 'لا يوجد رمز تحقق لهذا الرقم.'];
        }

        if ($otp->isExpired()) {
            return ['success' => false, 'message' => 'انتهت صلاحية الرمز. يرجى طلب رمز جديد.'];
        }

        if ($otp->code !== $code) {
            return ['success' => false, 'message' => 'رمز التحقق غير صحيح.'];
        }

        $otp->update(['is_used' => true]);

        return ['success' => true, 'message' => 'تم التحقق بنجاح.'];
    }

    // ─── إرسال SMS عبر iSend ──────────────────────────────────────
    private static function sendSms(string $phone, string $code): bool
    {
        try {
            $message = "GoFull - رمز التحقق الخاص بك هو: {$code}\nصالح لمدة " . self::OTP_EXPIRY_MINUTES . " دقائق. لا تشاركه مع أحد.";

            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://isend.com.ly/api/http/sms/send', [
                'api_token' => config('services.isend.api_token'),
                'recipient' => $phone,
                'sender_id' => config('services.isend.sender_id'),
                'type'      => 'plain',
                'message'   => $message,
            ]);

            $body = $response->json();

            if (($body['status'] ?? '') === 'success') {
                Log::info('OTP SMS sent', ['phone' => $phone]);
                return true;
            }

            Log::warning('iSend SMS failed', [
                'phone'    => $phone,
                'response' => $body,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('iSend SMS exception: ' . $e->getMessage(), ['phone' => $phone]);
            return false;
        }
    }

    // ─── إضافة مستخدم لـ Contacts (اختياري) ──────────────────────
    public static function addContact(User $user): void
    {
        try {
            Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://isend.com.ly/api/http/contacts/' . config('services.isend.group_id') . '/store', [
                'api_token'  => config('services.isend.api_token'),
                'PHONE'      => $user->phone,
                'FIRST_NAME' => $user->name,
            ]);
        } catch (\Exception $e) {
            Log::warning('iSend add contact failed: ' . $e->getMessage());
        }
    }
}