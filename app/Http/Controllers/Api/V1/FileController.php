<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $folder = $request->input('folder', 'uploads'); // Nếu không có thì mặc định là 'uploads'

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Kiểm tra kích thước file
            if ($file->getSize() > 5 * 1024 * 1024) { // 5 MB
                return Common::response(400, 'File size exceeds the maximum allowed size of 5MB');
            }

            if (!$file->isValid()) {
                return Common::response(400, 'Invalid file');
            }

            // Tạo tên file mới bằng cách thêm timestamp vào tên gốc
            $filename = time() . '_' . $file->getClientOriginalName();

            // Lưu file vào thư mục public với tên mới
            $path = $file->storeAs('public/' . $folder, $filename);

            $fullUrl = Config::get('app.url') . Storage::url($path);

            Upload::create([
                'url' => $fullUrl,
                'upload_by' => Auth::user()->username
            ]);

            return Common::response(200, 'File uploaded successfully', ['url' => $fullUrl]);
        }

        return Common::response(400, 'No file uploaded');
    }
}
