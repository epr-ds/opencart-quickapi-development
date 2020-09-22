# OpenCart QuickAPI Developer
REST API Module creator for OpenCart v3 Developers

## Features
This OpenCart Module allows to build your own "REST" API's in an easily way, just for testing, developing or learning coding APIs that could be consumed by client apps.

QuickAPI Developer "compiles" all modules added from the Extension/Module manager in one just single file and those functions defined in the module (or modules) marked as public could be consumed by the client, otherwise they won't be handled.

## How to install
1. Download repository
2. Unzip the downloaded file and compress as ".zip" the "upload" directory that comes within the downloaded. Having as follows: "upload.zip"
3. Rename the compressed file as you wish but using the following mandatory sufix _.ocmod_,
    * _e.g: QuickAPIDeveloper.api.ocmod_
4. Go to your OpenCart admin panel and then to Extensions > Installer, and finally upload the compressed file.
5. Go through extension manager, choose modules and look for QuickAPI Developer module, then press the _install_ button. That's it!

## How to use it
Once the module is installed, you can now add, edit or remove QuickAPI Developer modules from the extension manager which should be accesbile throught the users created under System > Users > API.

## Integration with OpenCart
It's very simple, just by creating new API users from the admin panel, go to System > Users > API ;)

### Client App Example
[Here](https://github.com/PerezRE/OpenCart-Quickadmin) a client app which uses created APIs with this module.

### Notice
All API modules are "compiled" in one file, so if you shared your API user and key, the final user is allowed to access to all functions coded.

#### Warning & Recommendations
This module doesn't build a real REST API, so the requested operations are handled through the query parameter called ``route``.

Despite the module doesn't hurt any OpenCart files or data from DB. I highly recommend that this module must not be a deployable module for a store in production at least you know what you are doing!

This module was built either for self-taught or practicing API development. 
So as is told, It's just for developing or testing app clients, that means allows you grab and set data from the system.

I highly recommend to read [OpenCart DevDocs](http://docs.opencart.com/en-gb/developer/loading/) to know how coding can be improved using a world of built-in functions and classes made by OpenCart Team.