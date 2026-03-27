# Check-Off - Make Splitting Simple

## Team Members

Alonzo Rico, Lihang Jin, Nona Oi

## Overview
Check off is a Laravel Web application that allows users to either create events and assign users with a contribution owed or mark off contributions owed as a guest. Simply utilizing Firebase's easy to implement database features, we simply cross reference and check for the user's details, match their name to an existing event, and outline all of their existing contributions.

## Tech Stack

- Laravel
- Firebase

## Prerequisites
In order to run our application, you need a few things

1. Node.js
2. npm (Node.js package manager)
3. Laravel Herd
4. Composer
5. Firebase
6. PHP
7. Git

## Installation

First, make sure to install the required dependencies. I'll link the websites to each dependency where you can follow the instructions to download each dependency.

### Git
<a href="https://git-scm.com/install/windows">Website</a>

For git, if you are on Windows,
- Make sure to set the Bin path in git as one of your environment variables.
- To do this, navigate to your Program Files and locate Git.
- Then, look for the bin file and copy the path.
- Afterwards, in your Windows Serach Bar, search for Environment Variables.
-  Once open, select Environment Variables. At the top, you will see lots of paths, look for "Path" and double click.
-  More paths will appear, from here click "New" and you will be prompted with a new path.
-  Paste the path to the Bin and save your changes

### Laravel  
<a href="https://laravel.com/docs/13.x/installation">Website</a>

### Node and npm
<a href="https://nodejs.org/en/download">Website</a>

### Laravel Herd
<a href="https://herd.laravel.com/windows">Website</a>

### Composer
<a href="https://getcomposer.org">Website</a>

For Composer, follow the same instructions like with git

### PHP
<a href="https://www.php.net/downloads.php">Website</a>

For PHP, follow the same instructions like with git

## What next?

After all the dependencies are installed, create a new folder in your desktop where you will clone our Repository.

Open a code editor (like Visual Studio Code) and open the folder you created. It should be in your Desktop.

Then, navigate back to this repo, click on "Code" and copy the link.

Go back to your editor, open your terminal, and run the following command

``git clone <repo-link>``

Now, you can open the folder named "Check-Off-CIS-3296" to see all the project files.

Afterwards, all the assets from the project should be installed. Now is best to run the following commands to actually install these dependencies:

``composer install``

``npm install``

``npm install firebase``

Once all the dependencies have been downloaded, we'll now link our project to our herd so that it can create a .test link

Run

```herd link```

which will create a herd folder that you can select when you open up Herd.

Open up herd, select that folder, and .test server should be running.

Finally, run the following command to actually start the server and start using the application

```npm run dev```


NOTE: In any case the Herd server isn't working, open another terminal within your editor. To do this, navigate to the "Terminal" section and click "New Terminal". while npm run dev is running, run the following command

```php artisan serve```

This will start a php development server and will also allow you to access the application. Just click on the link generated after running


## File Structure Overview (Files that were directly used)

- Resources: Folder that contains the main CSS, JS, and Pages involved in the project
- Views: Folder within Resources that holds all the pages in our application
- Auth: Folder within views that holds all the login, register, and other authentication related pages in our application
- Components/Layouts: Folder that contains persistent assets that are similar in every page. Will pull from another folder called "partials"
- Components/Layouts/Partials: Where components like "nav-bar" and "altnav-bar" reside
- Routes: Folder containing all the route connections to each page and controls access to those pages
- Storage/Firebase: Folder that contains the JSON which contains the provate key that allows access to the database
- .env: File that contains all sensative information like DB passwords, App ID, etc
- artisan: File that's interacted by php in order to write to certain folders it's given access to
- vite.config.js: config file that tells Vite what files to load on reload

## Attribution

- No external Libraries used
