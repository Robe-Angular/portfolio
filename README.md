
<p>Step 1. Create a database on mysql with the name tutorial_db</p>

  

<p>Step 2. Grant permissions to tutorial_user to the mysql Database</p>

  

<p>Step 3. To gen .env file and then generate the key, on the laravel directory run:</p>

  

```linux
	cp .env.example .env

	php artisan key:generate
```

  

<p>Step 4. Properly install and enable pdo_mysql if you use linux</p>

  

<p>Step 5. Generate JWT secret key with:</p>

  

```linux
	php artisan jwt:secret
```

  

<p>Step 6. Set .env database connection :</p>

  

```linux
	DB_CONNECTION=mysql
	DB_HOST=host_to_database
	DB_PORT=3306
	DB_DATABASE=tutorial_db
	DB_USERNAME=tutorial_user
	DB_PASSWORD=secret_tutorial
```

  

<p>Step 7. To start migrations, run:</p>

  

```linux
	php artisan migrate:install
```

  

<p>Step 8. To Update database-tables run:</p>
  

```linux
	php artisan migrate
```
<p>Step 9. Uncomment lines on <i>app/Http/Controllers/AdminController.php</i></p>

```php
	public function index(){
        
        
        $admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('LaravelJWT')//'LaravelJWT' Is the password
        ]);

        return response()->json(['message' => 'success'], 200);
	}
```

and on <i>routes/api.php uncomment</i> uncomment:

```php
	Route::get('/make-admin',[AdminController::class,'index']);
```
To allow register the admin.

Step 10. Go to http://{$host:port}/api/make-admin, this will save the admin on admins tables, the default password is "LaravelJWT" and then comment again the lines on <i>app/Http/Controllers/AdminController.php</i> and <i>routes/api.php </i> to stop generating admins.
