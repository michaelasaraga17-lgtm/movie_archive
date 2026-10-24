==============================================================================
MOVIE ARCHIVE
A movie discovery website built with HTML, Tailwind CSS, PHP and MySQL
==============================================================================

Student : ______________________________
Course  : ______________________________
Section : ______________________________
Date    : ______________________________


------------------------------------------------------------------------------
1. OVERVIEW
------------------------------------------------------------------------------
Movie Archive is a small, curated movie discovery site styled like an
independent film journal. Visitors can browse a featured film and a poster
grid, search by title, filter by genre and release year, open a detail view,
and save films to a personal watchlist. An admin area lets the site owner
add, edit and delete film records.

The site stores movie information and poster IMAGE LINKS only. It does not
store or stream any movie files or videos.


------------------------------------------------------------------------------
2. FEATURES
------------------------------------------------------------------------------
Public site (index.html)
  - Featured film section with a large poster and film details
  - Responsive poster grid (staggered layout)
  - Search by title
  - Filter by genre and by release year, plus sorting
    (newest, highest rated, title A to Z)
  - Detail panel: poster, synopsis, genres, director, cast, rating and
    release date
  - Watchlist: save and remove films; the sidebar has a "Watchlist" entry
    with a live count
  - Empty states (no results, empty watchlist, empty archive) and error
    messages when the database cannot be reached

Admin area (admin/)
  - Login with username and password
  - Film list with Edit and Delete
  - Add / Edit form with server-side validation and a live poster preview
  - Only one film can be "featured" at a time
  - Log out


------------------------------------------------------------------------------
3. TECHNOLOGIES
------------------------------------------------------------------------------
  - HTML5 and JavaScript (fetch API) for the public page
  - Tailwind CSS via CDN (no Node.js, npm or build step)
  - PHP 7.4 or newer (tested on PHP 8.3) with PDO
  - MySQL / MariaDB, managed through phpMyAdmin
  - XAMPP (Apache + MySQL + PHP) for running locally
  - Google Fonts: Bodoni Moda and Hanken Grotesk


------------------------------------------------------------------------------
4. REQUIREMENTS
------------------------------------------------------------------------------
  - XAMPP with Apache and MySQL
  - A web browser
  - An internet connection (needed for the Tailwind CDN, Google Fonts and
    the poster images, which are loaded from external links)


------------------------------------------------------------------------------
5. HOW TO RUN (XAMPP)
------------------------------------------------------------------------------
 1. Copy the whole "movie_archive" folder into:
        C:\xampp\htdocs\
    The result must be:  C:\xampp\htdocs\movie_archive\index.html
    (Make sure it is not nested as movie_archive\movie_archive\.)

 2. Open the XAMPP Control Panel and START both "Apache" and "MySQL".

 3. Open phpMyAdmin:   http://localhost/phpmyadmin

 4. Click the "Import" tab, choose the file "database.sql" from the project
    folder, and click "Import" (or "Go"). This creates the database
    "movie_archive", all tables, and a small sample dataset.

 5. Open the website:  http://localhost/movie_archive/

 6. Open the admin area:  http://localhost/movie_archive/admin/

If your MySQL root account has a password, edit includes/db.php and set
DB_PASS to that password.


------------------------------------------------------------------------------
6. ADMIN LOGIN (DEFAULT)
------------------------------------------------------------------------------
    Username : admin
    Password : admin123

These are default credentials for local testing. To change the password:

 1. Open a Command Prompt and run:
        C:\xampp\php\php.exe -r "echo password_hash('NEWPASSWORD', PASSWORD_DEFAULT);"
 2. Copy the text it prints.
 3. In phpMyAdmin, open the "admins" table, edit the "admin" row, and paste
    the text into the "password_hash" column.


------------------------------------------------------------------------------
7. FOLDER STRUCTURE
------------------------------------------------------------------------------
movie_archive/
  index.html            Public website (the only plain HTML page). Contains
                        the Tailwind CDN setup and all front-end JavaScript.
  database.sql          Creates the database, tables and sample data.
  README.txt            This file.
  api/
    movies.php          Movie list, search, filters, featured film, single
                        movie, and watchlist view (returns JSON)
    filters.php         Genres (with counts) and release years (JSON)
    watchlist.php       Get / add / remove watchlist items (JSON)
  includes/
    db.php              PDO database connection
    functions.php       JSON helpers, input helpers, visitor cookie
    auth.php            Admin session, login check, CSRF tokens, flash
                        messages
    admin_header.php    Shared top section of the admin pages
    admin_footer.php    Shared bottom section of the admin pages
  admin/
    login.php           Admin sign in
    logout.php          Admin sign out
    index.php           List of all films (Edit / Delete)
    edit.php            Add and edit form (one file for both)
    delete.php          Deletes a film (POST only)
  assets/               Reserved for local static files (currently empty)

How the pieces connect: index.html uses JavaScript fetch() to call the PHP
files in api/. Those files query MySQL and return JSON, and the page renders
the results. No PHP code is inside index.html.


------------------------------------------------------------------------------
8. DATABASE
------------------------------------------------------------------------------
Database name: movie_archive

  genres        id, name
  movies        id, title, release_date, rating, director, cast_list,
                synopsis, poster_url, is_featured, created_at
  movie_genres  movie_id, genre_id        (many-to-many link table; a film
                                           can have several genres)
  watchlist     id, visitor_id, movie_id, added_at
  admins        id, username, password_hash

Deleting a film automatically removes its genre links and watchlist entries
(ON DELETE CASCADE). The sample data contains 10 films and 8 genres.


------------------------------------------------------------------------------
9. API ENDPOINTS (all return JSON)
------------------------------------------------------------------------------
  GET  api/movies.php
         optional parameters:
           q=<text>        title search
           genre=<id>      filter by genre
           year=<yyyy>     filter by release year
           featured=1      the featured film only
           watchlist=1     only the current visitor's saved films
           id=<id>         a single film with full details

  GET  api/filters.php
         genres (with film counts) and the list of release years

  GET  api/watchlist.php
         ids of the films saved by the current visitor
  POST api/watchlist.php
         action=add|remove and movie_id=<id>


------------------------------------------------------------------------------
10. SECURITY AND VALIDATION
------------------------------------------------------------------------------
  - All database queries use PDO prepared statements.
  - Admin passwords are stored hashed (password_hash) and checked with
    password_verify. The session id is regenerated on login.
  - Every admin form that changes data uses a CSRF token.
  - Admin pages require login; unauthenticated visitors are redirected to
    the login page.
  - Output is escaped (htmlspecialchars in PHP, an escape function in the
    JavaScript) to prevent XSS.
  - Server-side validation in the add/edit form: required fields, maximum
    lengths, a valid date, a rating between 0 and 10, a valid http(s) poster
    link, and at least one genre. Browser-side validation is also used.
  - Watchlist ids and filters are validated as integers on the server.


------------------------------------------------------------------------------
11. NOTES AND LIMITATIONS
------------------------------------------------------------------------------
  - The watchlist is tied to the visitor's browser through a cookie. There
    are no visitor accounts, so it does not follow the person to another
    browser or device.
  - Poster images are links to external sites. If a link is dead, the page
    tries to look up the poster on Wikipedia, and if that also fails it
    shows a plain title card. To fix a poster permanently, edit the film in
    the admin area and paste a working image link.
  - There is a single admin role. Admin accounts are managed directly in the
    "admins" table.
  - The site needs an internet connection for the CDN, fonts and posters.


------------------------------------------------------------------------------
12. TROUBLESHOOTING
------------------------------------------------------------------------------
  "Server returned 404 for api/movies.php"
      The api folder is not next to index.html. Check the folder structure
      in section 7 and make sure the folder is not nested twice.

  "Database error. Is MySQL running and is database.sql imported?"
      Start MySQL in XAMPP and import database.sql in phpMyAdmin. The
      database must be named exactly: movie_archive

  Page has no styling
      The Tailwind CDN could not load. Check your internet connection.

  Cannot log in to the admin area
      Use the default login in section 6. If you changed the password,
      reset it using the steps in section 6.


------------------------------------------------------------------------------
13. CREDITS
------------------------------------------------------------------------------
  - Tailwind CSS (CDN): https://tailwindcss.com
  - Fonts: Bodoni Moda and Hanken Grotesk, from Google Fonts
  - Poster images belong to their respective rights holders and are linked,
    not hosted. Film information in the sample data is for demonstration.
