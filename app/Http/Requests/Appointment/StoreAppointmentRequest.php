<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'property_id' => 'required|exists:properties,id',
            'client_phone' => 'required|string|max:13',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|in:11:00,12:00,13:00,14:00,15:00,16:00,17:00,18:00' // سنتحقق من الصيغة في الخطوة التالية
        ];
    }


    // #[Override]
    public function messages()
    {
        return [
            'property_id.required' => 'حقل property_id مطلوب.',
            'property_id.exists' => 'العقار المحدد غير موجود.',
            'client_phone.required' => 'حقل client_phone مطلوب.',
            'client_phone.string' => 'حقل client_phone يجب أن يكون نصاً.',
            'client_phone.max' => 'حقل client_phone يجب ألا يتجاوز 13 حرفاً.',
            'appointment_date.required' => 'حقل appointment_date مطلوب.',
            'appointment_date.date' => 'حقل appointment_date يجب أن يكون تاريخاً صالحاً.',
            'appointment_date.after_or_equal' => 'عذراً، لا يمكنك حجز موعد في تاريخ قديم، يرجى اختيار تاريخ اليوم أو تاريخ مستقبلي.',
            'appointment_time.required' => 'حقل appointment_time مطلوب.',
            'appointment_time.in' => 'حقل appointment_time يجب أن يكون ضمن الأوقات المسموح بها (11:00, 12:00, 13:00, 14:00, 15:00, 16:00, 17:00, 18:00).',
        ];
    }
}
