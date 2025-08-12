## REYHAN-WEDDING-API

This is the API for weeding management content

## Setup

1. Setup Composer
    - **[see doc here](https://getcomposer.org/doc)**
2. Setup Laravel (v8x)
    - **[see doc here](https://laravel.com/docs/8.x)**
3. Setup PHP (v7.3 or v8.0), v8.0 recommended
    - **[see doc here](https://www.php.net/docs.php)**
4. Setup Database (for newbie recomended setup MySql for Database)
    - **[see doc MySql here](https://dev.mysql.com/doc)**
5. Setup GIT
    - **[see doc here](https://docs.github.com/en/get-started/quickstart/set-up-git)**
6. Clone Project
    - Clone via SSH / Https
        - SSH Run Command: git clone git@github.com:your-gitlab-username/your-project-name.git
        - https Run Command: git clone https://github.com/your-gitlab-username/your-project-name.git
7. create file .env on dir project (copy from .env.example)
    - What variables need to be edited?
        - APP_NAME=YourNameApp
        - APP_ENV=yourEnviroment (sample: local, development, staging, production)
        - APP_DEBUG=true (for enviroment production it should be false)
        - APP_URL=yourUrlApps (default http://localhost:8000)
        - APP_URL=http://localhost #Do not with slash (/) at the end (your API your main)
        - APP_TIMEZONE="Asia/Jakarta" (your timezone, here default Asia/Jakrta)
        - APP_FE_URL=http://localhost:3000/ #With slash (/) at the end (your FE domain) 
        - DB_CONNECTION=mysql (Your database connection)
        - DB_HOST=127.0.0.1 (Your Database url / ip)
        - DB_PORT=3306 (Your Database port)
        - DB_DATABASE=sampleDatabase (your DB password)
        - DB_USERNAME=sampleUsernameDatabase (Your Database username)
        - DB_PASSWORD=samplePasswordDatabase (Your Database password)
        - MAIL_MAILER (need setup SMTP account first)
        - MAIL_HOST= (need setup SMTP account first)
        - MAIL_PORT= (need setup SMTP account first)
        - MAIL_USERNAME= (need setup SMTP account first)
        - MAIL_PASSWORD= (need setup SMTP account first)
        - MAIL_ENCRYPTION= (need setup SMTP account first)
        - MAIL_FROM_ADDRESS= (need setup SMTP account first)
        - MAIL_FROM_NAME= (need setup SMTP account first)
        - EMAIL_ADMIN="yourEmail@gmail.com" (Your email for login app as Admin)
        - PHONE_ADMIN="+628xxxxxx" (Your phone number)
        - NAME_ADMIN="yourName as Admin"
        - EMAIL_STAFF="yourEmail@gmail.com" (dont same with EMAIL_ADMIN)
        - PHONE_STAFF="+628xxxxxx" (dont same with PHONE_ADMIN)
        - NAME_STAFF="yourName as Staff"
        - PHONE_WHATSAPP="+628xxxxxx" (Your whatsapp admin)
    - For MAIL (Mailer / SMTP) your need Mailer / SMTP credentials, how to setup using gmail?
        - go to **[here](https://myaccount.google.com/security)**
        - 2nd Verification should be on or active (klik arrow right or option to open setting 2nd Verification)
        - Follow the instruction from google and make sure use active phone number for OTP
        - After it activated, and you got to the setting 2nd Verification again, go to the bottom of page there is should be Application Password section
        - Go there and Fill App name, the you will get password, copy and save (to note or anything) it for your SMTP Password
        - After all of it, go to .env edit this variable:
            - MAIL_MAILER=smtp (default smtp)
            - MAIL_HOST=smtp.gmail.com (default smtp.gmail.com)
            - MAIL_PORT=587 (default 587)
            - MAIL_USERNAME="yourEmail@gmail.com" (Enter the email that has been set to 2nd verification)
            - MAIL_PASSWORD="yourPassword" (Password that you were copied before while activating 2nd Verification)
            - MAIL_ENCRYPTION=tls (default tls)
            - MAIL_FROM_ADDRESS="yourEmail@gmail.com" (Enter the email that has been set to 2nd verification)
            - MAIL_FROM_NAME="yourName" (Enter your name or app name, it will show as email from when the receiver get email from your app)
    - The reset Enviroment you can set as default by .env.example
8. Install Package
    - run command: composer install
9. Generate key laravel
    - run command: php artisan key:generate
10. Migate tables
    - run command: php artisan migrate
11. Seed default data to tables
    - run command: php artisan db:seed
12. Run App
    - run command: php artisan server
    - you can try it using postman, download postman collection v2.1 & env postmane **[here](https://drive.google.com/drive/folders/1XLSRGPoL7u6zKmRmCfTREoE5AkxBV6bc?usp=drive_link)** (need request)
