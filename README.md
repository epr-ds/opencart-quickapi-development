# OpenCart QuickAPI Development
A very basic API creator for OpenCart APIs v3 Developers

## Features
This OpenCart Module allows to build your own code throught API's in an easily way, just for testing, developing or learning how to code APIs that could be consumed by client apps.

QuickAPI Developement builds all modules added from the Extension/Module/QuickAPI manager split into different files.

The OpenCart QuickAPI extension builds the code into different files based on OpenCart v3 MVC-L standard: controller, model and language, whose are located into `catalog/controller/api/api_name.php`, `catalog/model/extension/module/quickapi/api_name.php`, `catalog/language/en-gb/api/api_name.php`, respectively.

The extension has with already built-in functions such as `processRequest` into the controller for handling the request based on the type, i.e: `GET`, `POST`, `PUT` and `DELETE`. (Read the code for more details).

This extension allows to build code asynchronously so that it reflects change inmediately.

## How to install
1. Download repository
2. Unzip the downloaded file and compress as ".zip" the "upload" directory that comes within the downloaded. Having as follows: "upload.zip"
3. Rename the compressed file as you wish but using the following mandatory sufix _.ocmod_,
    * _e.g: QuickAPIDeveloper.api.ocmod_
4. Go to your OpenCart admin panel and then to Extensions > Installer, and finally upload the compressed file.
5. Go through extension manager, choose modules and look for QuickAPI Developer module, then press the _install_ button. That's it!

## How to use it
Once the module is installed, you can now add, edit or remove QuickAPI Modules from the extension manager under the type of Modules, once you've added/edited/deleted modules and created an user through System > Users > API to grant access, they should be accesbile for any request.

## Integration with OpenCart
It's very simple, just by creating new API users from the admin panel, go to System > Users > API ;)

### Client App Example
[Here](https://github.com/PerezRE/opencart-quickadmin) a client app that uses created APIs with this module.

#### Warning & Recommendations
This module doesn't build a real REST API, so the requested operations are handled through the query parameter called `route` by OpenCart which means that a request must have the following URL format: `https://myopencart.store/route=api/my_api_name`.

### Notice
Despite the module doesn't hurt any OpenCart files or data from DB. I highly recommend that this module must not be a deployable module for a store in production at least you know what you are doing!

This module was built for self-taught abd practicing API development.
So as is told, It's just for developing or testing app clients, that means allows you grab and set data with custom format from the system.

I highly recommend to read [OpenCart DevDocs](http://docs.opencart.com/en-gb/developer/loading/) to know how coding can be improved using a world of built-in functions and classes made by OpenCart Team.



## Screenshots
![Example QuickAPI Modules](https://i.ibb.co/Czjf1Kn/quickapi-modules.png "Created QuickAPI Modules")
![Example Editing/Creating QuickAPI Module](https://i.ibb.co/X7ncSpC/quickapi.png "Creating/Editing Quick API module")