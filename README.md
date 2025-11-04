This is student project following LAB UAS AL00CO19-3003

Here are some instructions on how to start the project on your machine. 

Important note if you will use Git please do not commit and push anything without our discussion. Thanks.



There are 2 options download project from Teams or from GitHub 



Teams 

&nbsp; 

GitHub 



and also advanced) method use Git and GitHub Desktop



Create Project folder on your laptop C:\\Users\\Public\\DWD



Install Xampp inside C:\\Users\\Public\\DWD\\xampp

note: C:\\Users\\Public\\DWD\\xampp\\apache-right C:\\Users\\Public\\DWD\\xampp\\xampp\\apache-wrong



if you don’t have and you wish, install Git https://git-scm.com/install/windows 



if you don’t have and you wish, install GitHub  https://desktop.github.com/download/  than sign in



Do 5 and 6  if you installed git and will try to use github otherwise skip it.



Create the project folder and init repo for Git. in CMD:

cd C:\\Users\\Public\\DWD

mkdir MealPlanning

cd MealPlanning

git init -b main



GitHub Desktop settings (be signed up):

&nbsp;File → Clone repository… → URL tab →https://github.com/AlekMarkin/MealPlanning/ → Clone. 



Also choose C:\\Users\\Public\\DWD\\MealPlanning as path during cloning



Check if the files appear in the folder (C:\\Users\\Public\\DWD\\MealPlanning)





If you decided download manually  from Teams  just unzip project in same directory 

C:\\Users\\Public\\DWD\\MealPlanning



Only after you see the project in the folder in your laptop and install xampp  go on.







To configure our work place, we need to change some files. You'll find these files in the “Files” folder (C:\\Users\\Public\\DWD\\MealPlanning\\files). Replace them where I tell you below.



Replace files in Xampp (in files folder xampp\_conf with these files)

C:\\Users\\Public\\DWD\\xampp\\apache\\conf\\httpd.conf

C:\\Users\\Public\\DWD\\xampp\\apache\\conf\\extra\\httpd-ssl.conf

C:\\Users\\Public\\DWD\\xampp\\apache\\conf\\extra\\httpd-xampp.conf

C:\\Users\\Public\\DWD\\xampp\\php\\php.ini

C:\\Users\\Public\\DWD\\xampp\\mysql\\bin\\my.ini

C:\\Users\\Public\\DWD\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf 



7.1 Start Apache and MySql



Admin panel must be there http://127.0.0.1:8080/phpmyadmin  



Replace .env in App (take also in files)

C:\\Users\\Public\\DWD\\MealPlanning\\app



Go to Command Prompt check do you have  Composer in CMD:

cd C:\\Users\\Public\\DWD\\MealPlanning\\app

composer -V



if not: 

winget install --id Composer.Composer -e



try migrate DB   in CMD (you must be here C:\\Users\\Public\\DWD\\MealPlanning\\app)

php artisan migrate



if something wrong and you see a lot of red and Db hasn’t appeared in phpMyAdmin we have one more way just IMPORT .sql from folder Files in phpMyAdmin



create key for interaction with DB in CMD (you must be here C:\\Users\\Public\\DWD\\MealPlanning\\app)

php artisan key:generate



Go to http://127.0.0.1:8080/  Home page, I hope it works

































