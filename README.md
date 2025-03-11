### init

- xampp install
- composer install
- composer create-project --prefer-dist laravel/laravel {project_name}
- 또는 composer global require laravel/installer 후 laravel new
- generate app key -> php artisan key:generate

### debug mode setting

- {php_path}/ext에 XDebug.dll 파일 위치
- php path에 위치한 php.ini 파일에 XDebug 프로퍼티 추가
- php.ini 수정

[xDebug]
zend_extension=C:\xampp\php\ext\{xdebug_dll_name.dll}
xdebug.mode=debug
xdebug.client_port=9000
xdebug.client_host=localhost
xdebug.start_with_request=yes
xdebug.profiler_append=0
xdebug.profiler_enable=1
xdebug.profiler_enable_trigger=0
xdebug.profiler_output_dir="C:\xampp\tmp"
xdebug.profiler_output_name="cachegrind.out.%t-%s"
xdebug.remote_enable=1
xdebug.remote_handler="dbgp"
xdebug.remote_host="127.0.0.1"
xdebug.remote_port=9000
xdebug.trace_output_dir="C:\xampp\tmp"



### PHP 네이밍 컨벤션


| 타입         | 컨벤션                 | 예시                                      |
| ------------ | ---------------------- | ----------------------------------------- |
| 변수         | 카멜 케이스            | `$userName`, `$totalAmount`               |
| 상수         | 대문자 스네이크 케이스 | `MAX_SIZE`, `DEFAULT_VALUE`               |
| 함수         | 카멜 케이스            | `getUserInfo()`, `calculateTotal()`       |
| 클래스       | 파스칼 케이스          | `UserProfile`, `PaymentGateway`           |
| 메소드       | 카멜 케이스            | `getUserName()`, `setEmail()`             |
| 인터페이스   | 파스칼 케이스          | `LoggerInterface`, `PaymentProcessor`     |
| 트레이트     | 파스칼 케이스          | `Loggable`, `Cacheable`                   |
| 네임스페이스 | 파스칼 케이스          | `App\Controllers`, `App\Models`           |
| 파일명       | 스네이크 케이스        | `user_profile.php`, `payment_gateway.php` |
| 디렉토리     | 스네이크 케이스        | `controllers/`, `models/`                 |

### artisan commands

#### php artisan serve

- 로컬 개발 서버 실행

#### php artisan migrate

- 데이터베이스 마이그레이션을 실행하여 스키마 변경
- --env=testing -> 테스트 DB에 생성

#### php artisan make:model ModelName

- 모델 클래스 생성
- `-a` option -> 모델, 마이그레이션, 컨트롤러, 팩토리(테스트 데이터 생성을 위한 템플릿), 시더(데이터베이스에 초기 데이터를 채우는데 사용) 생성

#### php artisan make:controller ControllerName

- 컨트롤러 클래스 생성

#### php artisan route:list

- 앱의 모든 라우트 경로 나열

#### php artisan config:cache

- 설정 파일 캐시

#### php artisan make:migration migration_name

- 마이그레이션 파일 생성

#### php artisan db:seed

- Seeder 실행

* `php artisan db:seed --class=UserSeeder`: 특정 시더 실행
* `php artisan migrate --seed`: 마이그레이션 후 시딩 실행
* `php artisan make:seeder UserSeeder`: 새로운 시더 클래스 생성

#### php artisan migrate:refresh

- 모든 마이그레이션을 하나씩 롤백한 후 다시 실행

#### php artisan migrate:fresh

- 모든 테이블을 한번에 drop하고 처음부터 다시 실행
- --seed option -> 테스트용 더미 데이터

#### php artisan list

- 그 외 커맨드 나열

#### php artisan install:api

- api.php 생성

#### php artisan l5-swagger:generate

- swagger 파일 생성 -> http://localhost:{port}/api/documentation#/

#### php artisan route:list
- --path=/api/admin -> 경로가 포함된 route만 조회

#### php artisan make:migration add_to_product_qnas_table --table=product_qnas
- --table={existing_table} -> 해당 테이블에 업데이트


### Scribe(PHP API Docs)

- composer require --dev knuckleswtf/scribe
- php artisan vendor:publish --provider="Knuckles\Scribe\ScribeServiceProvider"
- php artisan scribe:generate

### Eloquent setting(Eloquent method IDE autocomplete)

- composer require --dev barryvdh/laravel-ide-helper
- php artisan ide-helper:generate
- php artisan ide-helper:models -RW

### PHPUnit

- ./vendor/bin/phpunit tests -> 모든 Test 파일 실행
- ./vendor/bin/phpunit tests/{filename}.php -> 단위 파일 실행

### Filament

- php artisan vendor:publish --tag=filament-config
- php artisan make:filament-user -> 관리자 계정 생성
- php artisan make:filament-resource {ModelName} -> Filament 리소스 생성
- php artisan make:filament-widget {WidgetName}
- /admin 접근 안될 시 filament.php에서 'path' => env('FILAMENT_PATH', {route_name})으로 변경해서
### etc

- dd(arg): dump and die -> 객체 출력 후 프로세스 종료
- Get-Content -Path "storage\\logs\\laravel.log" -Wait -> tail -f
- jwt ref: https://medium.com/@a3rxander/how-to-implement-jwt-authentication-in-laravel-11-26e6d7be5a41
