This is student project following LAB UAS AL00CO20-3003 Dynamic Website Development Project 7.10.2025-9.12.2025

Teachers: Aki Vainio, Jan-Erik Sandelin. 

Created by students: 
Aleksei Markin|ID2400052
Azadeh Gholamihesari|ID2400066
Evgeniia Petrova|ID2317541
Vindya Perera|ID2400062


These instructions were originally written for students participating in the project, for an initial, test deployment of the production environment and for running the project on their computers. 
Since many project participants encountered difficulties configuring XAMPP, it was decided to set up identical settings and include the XAMPP, configuration files in the shared project folder for easy replacement (copy-past) on their own machines.

You will need: XAMPP (MySQL/MariaDB/PHP 8.2+), Composer (Laravel framework), GIT and GITHUB optionally.

Here are some instructions on how to get the project running on your machines.
Important note if you will use Git please do not commit and push anything without our discussion. Thanks.

There are 2 options download project from Teams or from GitHub

Teams
 
GitHub

and also advanced) method use Git and GitHub Desktop

Create Project folder on your laptop C:\Users\Public\DWD

2. Install Xampp inside C:\Users\Public\DWD\xampp
Note: C:\Users\Public\DWD\xampp\apache-right C:\Users\Public\DWD\xampp\xampp\apache-wrong

3. if you don’t have and you wish, install Git https://git-scm.com/install/windows

4. if you don’t have and you wish, install GitHub  https://desktop.github.com/download/  than sign in

5. Do 5 and 6  if you installed git and will try to use github otherwise skip it.

Create the project folder and init repo for Git. in CMD:
cd C:\Users\Public\DWD
mkdir MealPlanning
cd MealPlanning
git init -b main

6. GitHub Desktop settings (be signed up):
 File → Clone repository… → URL tab →https://github.com/AlekMarkin/MealPlanning/ → Clone.

Also choose C:\Users\Public\DWD\MealPlanning as path during cloning

Check if the files appear in the folder (C:\Users\Public\DWD\MealPlanning)

If you decided download manually  from Teams  just unzip project in same directory
C:\Users\Public\DWD\MealPlanning

Only after you see the project in the folder in your laptop and install xampp  go on.



7. To configure our work place, we need to change some files. You'll find these files in the “Files” folder (C:\Users\Public\DWD\MealPlanning\files). Replace them where I tell you below.

Replace files in Xampp (in files folder xampp_conf with these files)
C:\Users\Public\DWD\xampp\apache\conf\httpd.conf
C:\Users\Public\DWD\xampp\apache\conf\extra\httpd-ssl.conf
C:\Users\Public\DWD\xampp\apache\conf\extra\httpd-xampp.conf
C:\Users\Public\DWD\xampp\php\php.ini
C:\Users\Public\DWD\xampp\mysql\bin\my.ini
C:\Users\Public\DWD\xampp\apache\conf\extra\httpd-vhosts.conf

Start Apache and MySql

Admin panel must be there http://127.0.0.1:8080/phpmyadmin

8. Replace .env in App (take also in files)
C:\Users\Public\DWD\MealPlanning\app

9. Go to Command Prompt check do you have  Composer in CMD:
cd C:\Users\Public\DWD\MealPlanning\app
composer -V

if not:
winget install --id Composer.Composer -e

10. try migrate DB in CMD (you must be here C:\Users\Public\DWD\MealPlanning\app)
php artisan migrate

if something wrong and you see a lot of red and Db hasn’t appeared in phpMyAdmin we have one more way just IMPORT .sql from folder "Files" in phpMyAdmin

11. create key for interaction with DB in CMD (you must be here C:\Users\Public\DWD\MealPlanning\app)
php artisan key:generate

Finally, go to http://127.0.0.1:8080/  Home page, I hope it works

You might auth with:

login: markin@mail.com
Password: qwerty

or create your own user

Next time to launch project to do follow steps:

1. Start Apache and MySql
2. In CMD:
	cd C:\Users\Public\DWD\MealPlanning\app
	php artisan serve
3. visit http://127.0.0.1:8080/ in browser

