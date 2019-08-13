# OpenCart-Quickadmin-API
API Module for OpenCart v3

## Features
This OpenCart Module allows to create, modify and remove API modules in an easily way for testing or developing client apps.

## How to install

1. Download repository
2. Create a directory named "upload"
3. Unzip the file content into the upload directory
4. Compress upload directory as _.zip_
5. Rename the zip file as you wish but using the following sufix as mandatory _.ocmod_, 
    * _i.e: quickadmin.api.ocmod_
6. Go to your OpenCart admin panel and then to Extensions > Installer, and finally upload the compressed file.
7. Go throught extension manager, choose modules and look for Quickadmin API module, press the _install_ button. That's it!

## How to use it
Once the module is installed, you can now add, edit o remove API modules from the extension manager.

## Integration with OpenCart
It's simply by creating new API users from system > users > API ;)


### Notice
All API modules are "compiled" in one file, so if you shared your API user and key, the final user is allowed to access to all functions coded
