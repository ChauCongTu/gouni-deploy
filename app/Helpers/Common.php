<?php

namespace App\Helpers;

use App\Models\History;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\note;

class Common
{
    /**
     * Generate a standardized response for API calls.
     *
     * @param int $code The HTTP status code.
     * @param string $message The message to accompany the response.
     * @param mixed $data The main data payload to be included in the response.
     * @param mixed $paginateData Additional data for pagination if applicable.
     * @param string|null $bonus_name Optional bonus parameter name to include in the response.
     * @param mixed|null $bonus_value Optional bonus parameter value to include in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the standardized structure.
     */
    public static function response(int $code = 200, string $message, $data = null, $paginateData = null, $bonus_name = null, $bonus_value = null)
    {
        $response = [];
        if ($code === 200 || $code === 201) {
            $success = true;
        } else {
            $success = false;
        }
        $response['status'] = [
            'code' => $code,
            'success' => $success,
            'message' => $message
        ];
        if ($data) {
            if ($paginateData) {
                $resData = [
                    'data' => $data,
                    'paginateData' => $paginateData
                ];
            } else {
                $resData = [$data];
            }
            $response['data'] = $resData;
        }

        if ($bonus_name && $bonus_value) {
            $response[$bonus_name] = $bonus_value;
        }

        $response['time'] = Carbon::now();

        return response()->json($response);
    }

    /**
     * Lưu lịch sử với kết quả được xử lý thành chuỗi JSON.
     *
     * @param int $user_id ID của người dùng
     * @param string $model Tên model
     * @param int $foreignKey Khóa ngoại
     * @param array|string|Collection $result Kết quả
     * @param string|null $note Ghi chú
     * @return bool Trả về true nếu lưu thành công, ngược lại trả về false.
     */
    public static function saveHistory(int $user_id, string $model, int $foreignKey, $result, ?string $note = null): History
    {
        if ($result instanceof Collection) {
            $result = $result->toArray();
        }
        if (is_array($result)) {
            $result = json_encode($result);
        }

        $history = History::create([
            'user_id' => $user_id,
            'model' => 'App\Models\\' . $model,
            'foreign_id' => $foreignKey,
            'result' => $result,
            'note' => $note
        ]);
        $history['user'] = User::find($history['user_id']);
        $history['result'] = json_decode($history['result']);

        return $history;
    }

    /**
     * Calculate the difference between two given date times and return the result in the appropriate unit based on the difference.
     *
     * @param string $startDateTime The starting date time.
     * @param string|null $endDateTime The ending date time. Default is null, which means current date time (now).
     * @param string|null $unit The unit for the result ('days', 'hours', or 'minutes'). Default is null, which means auto-detect.
     * @return int|string The difference between the two date times in the specified unit, or a descriptive string if no unit is specified.
     */
    public static function timeDifference($startDateTime, $endDateTime = null, $unit = null)
    {
        $start = Carbon::parse($startDateTime);
        $end = $endDateTime ? Carbon::parse($endDateTime) : Carbon::now();

        if ($unit) {
            switch ($unit) {
                case 'days':
                    return $end->diffInDays($start);
                case 'hours':
                    return $end->diffInHours($start);
                case 'minutes':
                    return $end->diffInMinutes($start);
                default:
                    return $end->diffInHours($start);
            }
        } else {
            $difference = $end->diffInSeconds($start);
            if ($difference > 60 * 60 * 24) {
                return $end->diffInDays($start) . ' days';
            } elseif ($difference > 60 * 60) {
                return $end->diffInHours($start) . ' hours';
            } else {
                return $end->diffInMinutes($start) . ' minutes';
            }
        }
    }
    /**
     * Chuyển đổi thời gian từ giờ hoặc phút sang giây hoặc ngược lại.
     *
     * @param int $time Thời gian cần chuyển đổi
     * @param string $from Đơn vị thời gian đang ở (h: hours, m: minutes, s: seconds)
     * @param string $to Đơn vị thời gian muốn chuyển đổi đến (h: hours, m: minutes, s: seconds)
     * @return int Thời gian sau khi chuyển đổi
     */
    public static function convertTime(int $time, string $from, string $to): int
    {
        switch ($from) {
            case 'h':
                $time *= 3600; // 1 giờ = 3600 giây
                break;
            case 'm':
                $time *= 60; // 1 phút = 60 giây
                break;
            default:
                // Không cần thay đổi nếu là giây
                break;
        }

        switch ($to) {
            case 'h':
                $time /= 3600; // 1 giờ = 3600 giây
                break;
            case 'm':
                $time /= 60; // 1 phút = 60 giây
                break;
            default:
                // Không cần thay đổi nếu là giây
                break;
        }

        return (int) $time;
    }

    public static function stringToDatetime($dateTimeString)
    {
        return Carbon::parse($dateTimeString);
    }
    public static function stringToTimestamp($dateTimeString)
    {
        return Carbon::parse($dateTimeString)->timestamp;
    }
    public static function timestampToDatetime($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp);
    }
}
