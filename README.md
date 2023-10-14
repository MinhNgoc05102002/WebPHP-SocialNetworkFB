<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Cách chạy dự án
- Bước 1: git pull origin
- Bước 2: chạy lệnh composer i
- Bước 3: php artisan migrate 
- Bước 4: vào .env tìm để thay thông tin db tương ứng
- Bước 5: php artisan serve

##  Cách chạy được folder để ảnh
- Bước 1: php artisan storage:link
- Bước 2: tạo ra file media trong đấy


## Cách lấy User hiện tại trong token
* Cách 1: Để trong private router

- Bước 1: use Illuminate\Support\Facades\Auth;
- Bước 2: $username = auth()->user()->username;

* Cách 2: check token hợp lệ không qua token

- Bước 1: Phải settup Bears token
- Bước 2: gọi qua api http://127.0.0.1:8000/api/check-login phương thức GET

## Cách login trong Postman
- Bước 1: Chọn API : http://127.0.0.1:8000/api/login
- Bước 2: Bấm vào "Headers" thêm trường Accept: application/json để nó chỉ nhận json thôi
- Bước 3: Nhập email or username và password có trong db
- Bước 4: Nếu thành công sẽ trả về mã token
- Bước 5: Qua router private vd như : http://127.0.0.1:8000/api/post/get-list?page_index=1&page_count=10&username=tra-vh
- Bước 6: bấm vào "Authorization" -> chọn Type "Bearer Token" -> paste token mới tạo từ login vào
- Bước 7 RUNNNN !!!

## Cách tạo request validate
Bước 1: php artisan make:request {Tên request}

<h3>Rule validate ở đây - có thể custom để 1 vài trường hợp sẽ không validate nữa</h3>
 public function rules()
    {

        // Luật xác thực chung cho cả tạo và cập nhật
        $commonRules = [
            'username' => 'required|string',
            'function' => 'required|string',
        ];

        if ($this->input('function') === 'C') {
            // Áp dụng luật xác thực riêng cho thêm mới
            return array_merge($commonRules, [
                'content' => 'required|string|',
                'audience_type' => 'required|string',
            ]);

        } elseif($this->input('function') === 'U') 
        {
            // Áp dụng luật xác thực riêng cho tạo mới
            return array_merge($commonRules, [
                // Luật xác thực cho tạo mới
                'id_post' => 'required|string',
                'content' => 'required|string|',
                'audience_type' => 'required|string',
                'media' => 'required|string'
            ]);
        } elseif($this->input('function') === 'D') 
        {
            // Áp dụng luật xác thực riêng cho tạo mới
            return array_merge($commonRules, [
                // Luật xác thực cho xóa
                'id_post' => 'required|string',
            ]);
        }

        return $commonRules;
    }

<h3>Custome message ở đây</h3>
    public function messages()
    {
        return [
            'content.required' => 'content không được để trống',
            'audience_type.required' => 'audience_type không được để trống',
            'username.required' => 'username không được để trống',
            'id_post.required' => 'id_post không được để trống'
        ];
    }

