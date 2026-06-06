<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'message', 'is_read'];

    /**
     * دالة مساعدة لإنشاء إشعار جديد بسرعة في أي مكان بالكود
     */
    public static function send($userId, $title, $message)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}
