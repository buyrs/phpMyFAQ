# 2. Installation

## Requirements for phpMyFAQ

phpMyFAQ addresses a database system via PHP. In order to install it locally you will need a web server that meets the
following requirements:

- **[PHP](http://www.php.net)**
  - version 8.1 or later
  - memory_limit = 128M (the more the better)
  - cURL support
  - GD support
  - XMLWriter support
  - JSON support
  - Filter support
  - SPL support
  - FileInfo support
- **Web server** ([Apache](http://httpd.apache.org) 2.x or [nginx](http://www.nginx.net/) 1.0+)
- **Database server**
  - [MySQL](http://www.mysql.com) (via MySQLi extension)
  - [PostgreSQL](http://www.postgresql.org)
  - [Microsoft SQL Server](http://www.microsoft.com/sql/) 2012 and later
  - [SQLite](http://www.sqlite.org)
  - [MariaDB](http://montyprogram.com/mariadb/) (via MySQLi extension)
  - [Percona Server](http://www.percona.com) (via MySQLi extension)
- **Search engines** (optional)
  - [Elasticsearch](https://www.elastic.co/products/elasticsearch) 6.x or later
- correctly set: access permissions, owner, group
- **Docker** (optional)

You can only run phpMyFAQ successfully with constraints affect the directives open_basedir and disable_functions, which
can be set in the central php.ini or the httpd.conf respectively.

In case PHP runs as module of the Apache, you will have to be able to do a chown on the files before installation. The
files and directories must be owned by the web server's user.

You can determine which versions your web server is running by creating a file called **info.php** with the following
content: `<?php phpinfo(); ?>`

Upload this file to your webspace and open it using your browser. The installation-script checks which version of PHP
is installed on your server. Should you not meet the requirements, you cannot start the installation process.

In case you're running PHP before 8.1 you cannot use phpMyFAQ.

phpMyFAQ uses a modern HTML5/CSS3 powered markup. The supported browsers are the latest Mozilla Firefox
(Windows/macOS/Linux), the latest Safari (macOS/iOS), the latest Chrome (Windows/macOS/Linux), the latest Opera
(Windows/macOS/Linux) and Microsoft Edge (Windows/macOS/Linux).

We recommend to use always the latest version of Firefox, Chrome, Safari, Opera or Microsoft Edge.

## Preparations

### Classic Shared Web Hosting

You can install phpMyFAQ via one of the provided packages as .zip or .tar.gz or using Git. If you choose our package,
download it and unzip the archive on your hard disk.

If you want to use Git, please run the following commands on your shell:

    $ git clone git@github.com:thorsten/phpMyFAQ.git 3.2
    $ cd phpMyFAQ
    $ curl -s https://getcomposer.org/installer | php
    $ php composer.phar install
    $ curl -o- -L https://yarnpkg.com/install.sh | bash
    $ yarn install
    $ yarn build

You can modify the layout of phpMyFAQ using templates. A description of how this is done can be found [here](). Copy
all unzipped files to your web server in a directory using FTP. A good choice would be the directory **faq/**.

**Important:**
Writing permission for your script is needed in this directory to be able to write the file **config/database.php**
during installation. The installation script will stop when your web server isn't configured as needed.

It might help to set chmod 775 to the whole phpMyFAQ directory to avoid problems during the installation. If you're
running a very restrictive mod_php installation you should keep the chmod 775 for the following files and directories
even after the successful installation:

- the directory **attachments/**
- the directory **config/**
- the directory **data/**
- the directory **images/**

All other directories shouldn't be world-writable for your own security.

**Note**: If you're running SELinux, you may need further configuration or you should disable it at all.

The database user needs the permissions for CREATE, DROP, ALTER, INDEX, INSERT, UPDATE, DELETE and SELECT on all tables
in the database.

### Cloud Hosting via Docker

You first need a database, let's try with a MariaDB container:

    $ docker run -ti -n phpmyfaq-db mariadb

Then start the phpMyFAQ web application:

    $ docker run -ti --link phpmyfaq-db:db -p 8080:80 phpmyfaq/phpmyfaq

## Setup

Open your browser and type in the following URL:

`http://www.example.com/faq/setup/index.php`

### Step 1: Database server

Substitute **www.example.com** with your actual domain name. When the site is loaded, first select the database you want
to use for phpMyFAQ. The loaded database extensions from PHP are listed in a select box. Then enter the address of your
database server (e.g. db.provider.com), the database port, your database username and password as well as the database
name. The database have to be created with UTF-8 character set before running the installation script. You can leave the
prefix-field empty. If you are planning on using multiple FAQs in one database you will have to use a table prefix,
though (i.e. _sport_ for a sports FAQ, _weather_ for a weather FAQ, etc.). Please note that only letters and an
underline: "\_" can be used as the prefix. If you want to use SQLite, you only have to select a path to the database file
of SQLite.

### Step 2: LDAP or Microsoft Active Directory support

If PHP was compiled with the LDAP extension you can add your LDAP or Microsoft Active Directory information, too. Then
you can to insert your LDAP or Microsoft Active Directory information as well.

### Step 3: Elasticsearch support

If you want to use Elasticsearch, you can activate this in the third step. You just have to add at least one
Elasticsearch node and the index name.

### Step 4: Admin user setup

In addition, you can enter your language, default here is English. Furthermore, you should register your name, your
email address and - very importantly - your password. You must enter the password twice, and it has to be at least eight
characters long. Then click the button **"install"** to initialize the tables in your database.

## First Steps

You can enter the public area of your FAQ by entering

`http://www.example.com/faq/index.php`

into your browser's address field. Your FAQ will be empty and presented in the the standard layout.

To configure phpMyFAQ point your browser to

`http://www.example.com/faq/admin/index.php`

Use the username **admin** and your selected password for your first login into the admin section.

Some variables that does not change regularly, they can be edited in the file _config/constants.php_. You can change
the

- the time zone of your server (default: "Europe/Berlin")
- the timeout in the admin section (default: 300 minutes)
- the timeout warning pop-up in the admin section (default: 5 minutes)
- the solution id start value (default: 1000)
- the incremental value of the solution id (default: 1)
- the number of records in the Top10 (default: 10)
- the number of the latest records (default: 5)
- flag with which Google site map will be forced to use the current phpMyFAQ SEO URL schema (default: true)
- the number with which the Tags Cloud list is limited to (default: 50)
- the number with which the autocomplete list is limited to (default: 20)
- the default encryption type for passwords

## Notes regarding the search functionality

- The boolean full-text search will only work with MySQL and if there are some entries in the database (5 or more).
  The term you are looking for should also not be in more than 50% of all your entries, or it will automatically be
  excluded from search. This is not a bug, but rather a feature of MySQL.
- The search on other databases are using currently the LIKE operator.
- To improve the search functionality you should use Elasticsearch.

## Automatic content negotiation

To set the default language in your browser you have to set a variable that gets passed to the web server. How this is
done depends on the browser you are using.

- Mozilla Firefox: Tools -> Options -> Content -> Languages
- Google Chrome / Microsoft Edge / Opera\_ Settings -> Details -> Language settings
- Safari uses the macOS system preferences to determine your preferred language: System preferences -> International
  -> Language

## PHP settings

- We recommend using a PHP accelerator or OpCode cache
- Allocate at least 128 MB of memory to each PHP process
- Required extensions: GD, JSON, Session, MBString, Filter, XMLWriter, SPL, FileInfo
- Recommended configuration:

        memory_limit = 128M
        file_upload = on

## Enabling support for SEO-friendly URLs

_Apache Web server_

If you want to enable the search engine optimization you have to activate the mod_rewrite support in the admin backend
in the configuration page. You also have to edit the path information for the "RewriteBase". If you installed phpMyFAQ
on root directory "/" you should set in `RewriteBase /` Please check, if `AllowOverride All` is set correctly in your
httpd.conf file so that the .htaccess rules work.

_nginx Web server_

If you want to enable the search engine optimization you have to copy the rewrite rules in the file nginx.conf to your
nginx.conf. Then you have to activate the URL rewrite support in the admin backend in the configuration page.

## Enabling LDAP or Microsoft Active Directory support

If you're entered the correct LDAP or Microsoft Active Directory information during the installation you have to enable
the LDAP or Microsoft Active Directory support in the configuration in the admin backend. Now your user can authenticate
themselves in phpMyFAQ against your LDAP server or a Microsoft Active Directory server.

If you need special options for your LDAP or ADS configuration you can change the LDAP configuration in the admin
configuration panel.

If you want to add LDAP support later, you can use the file **config/ldap.php.original** as template and if you rename
it to **config/ldap.php** you can use the LDAP features as well after you enabled it in the administration backend.

## Using Microsoft Azure Active Directory

tbd.

## PDF export

Main features of the PDF export:

- supports all ISO page formats;
- supports custom page formats, margins and units of measure;
- supports UTF-8 Unicode and Right-To-Left languages;
- supports TrueTypeUnicode, OpenTypeUnicode, TrueType, OpenType, Type1 and CID-0 fonts;
- includes methods to publish some HTML code;
- includes graphic (geometric) and transformation methods;
- includes methods to set Bookmarks and print a Table of Content;
- supports automatic page break;
- supports automatic page numbering and page groups;
- supports automatic line break and text justification;
- supports JPEG and PNG images natively, all images supported by GD (GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM)

## Static solution ID

phpMyFAQ features a static solution ID which never changes. This ID is visible next to the question on a FAQ record
page. You may think why do you need such an ID? If you have a record ID _1042_ it is now possible to enter only the ID
_1042_ in the input field of the full-text search box, and you'll be automatically redirected to the FAQ record with the
ID _1042_. By default, the numbers start at ID **1000**, but you can change this value in the file _inc/constants.php_.
You can also change the value of the incrementation of the static IDs.

## Spam protection

phpMyFAQ performs these three checks on public forms:

1.  Check against IPv4 and IPv6 Network address
2.  Check against banned words
3.  Check against the captcha code (builtin or Google ReCaptcha)

The IPv4 and IPv6 Network addresses can be added or removed in the configuration panel in the administration backend.
If you want to add banned words to phpMyFAQ, then you have to edit the file _src/blockedwords.txt_. Please add only
one word per line.

By default, phpMyFAQ uses the builtin captcha functionality. If you want to use Google ReCaptcha v2, you can enable the
support for Google Recaptcha by adding your site and secret key. You can get the keys from
[Google](https://developers.google.com/recaptcha).

## Attachments

phpMyFAQ supports encrypted attachments. The encryption uses the [AES](http://en.wikipedia.org/wiki/Advanced_Encryption_Standard)
algorithm implemented in mcrypt extension (if available) or with native PHP Rijndael implementation. The key size vary
depending on implementation used and can be max 256 bits long. Use of mcrypt extension is strongly recommended because
of performance reasons, its availability is checked automatically at the run time.

Please be aware:

- Disabling encryption will cause all files be saved unencrypted. In this case you'll benefit sparing disk space,
  because identical files will be saved only once.
- Do not change the default attachment encryption key once files was uploaded. Doing so will cause all the previously
  uploaded files to be wrong decrypted. If you need to change the default key, you will have to re-upload all files.
- Always memorize your encryption keys. There is no way to decrypt files without a correct key.
- Files are always saved with names based on a virtual hash generated from several tokens (just like key and issue id
  etc), so there is no way to asses a file directly using the name it was uploaded under.
- Download continuation isn't supported.

## Twitter support

phpMyFAQ supports Twitter via OAuth. If you enable Twitter support in the social network configuration and add phpMyFAQ
as a Twitter application on [Twitter](https://dev.twitter.com/apps/new), all new FAQ additions in the administration
backend will also post the question of the FAQ, the URL of the FAQ and all tags as hashtags to Twitter, e.g. the tag
"phpMyFAQ" will be converted to "#phpmyfaq".

## Server side recommendations

**_MySQL / Percona Server / MariaDB_**

    interactive_timeout = 120
    wait_timeout = 120
    max_allowed_packet = 64M

## Syntax Highlighting

The bundled [highlight.js](https://highlightjs.org/) syntax highlighting component will find and highlight code inside
of &lt;pre&gt;&lt;code&gt; tags; it tries to detect the language automatically. If automatic detection doesn't work for
you, you can specify the language in the class attribute:

    <pre><code class="html">...</code></pre>

The list of supported language classes is available in the class reference. Classes can also be prefixed with either
language- or lang-.

To disable highlighting altogether use the "nohighlight" class:

    <pre><code class="nohighlight">...</code></pre>

## Elasticsearch Support

To improve the search performance and quality of search results it's possible to use Elasticsearch. You need a
running Elasticsearch instance accessible by phpMyFAQ via HTTP/REST. You can add the IP(s)/Domain(s) and port(s)
of your Elasticsearch cluster during installation or later by renaming the Elasticsearch file located in the folder
config/. If you choose to add this during installation, the file will be automatically written and the index will be
built. If you enabled Elasticsearch support in the admin configuration panel, you can create, re-import and delete your
index with a user-friendly interface.