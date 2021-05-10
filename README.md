# WPH Server Cache Helper
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg) 

WPH Server Cache Helper is a plugin that helps to prevent server Cache when the constant "DONOTCACHEPAGE" is set on a post or page.
This plugin has been created by WPHercules (https://wpherc.com and https://wpherc.es). 
I hope you find it useful.


## Important!
This plugin has been designed to work with server cache. It won´t do anything for cache based on plugins.
For this plugin to work properly, you may need to customise the cache configuration on your server. 

This will work for any server cache based using Nginx that is using the FastCGI Cache or Redis Page Cache. 
Litespeed servers already have this functionality included on their Litespeed plugin, so this is not required. 

## Features
* Add a "nocache" cookie to the pages or post that has the `DONOTCACHEPAGE` constant.
* Setup Cache-Control and Expires headers to prevent cache.
* Add an "special-secret" header to the page. Combined with custom Nginx code it will work with any page.
* Soon will work out of the box with Gridpane server cache (on their next update).
 
## Installation

The plugin can be installed like any other plugin directly from your plugins page.
Then you will need to do some extra work. 


## Server Configuration

By default we are creating a cookie and adding a header. 
The cookie default name is `wph_no_cache`
The default header is `wph_no_cache`

If you are using a different provider, we try to detect this using their custom plugin or their code. 
Right now we detect Kinsta, WPEngine, Siteground and the plugin Nginx Helper and we apply a different configuration for each of them.
If we do not detect any of those, then we apply our defaults.

For making this work you need to do 2 things:

1. Exclude the cache when the cookie is detected. An example of this code will be:


    	if ($http_cookie ~* "wph_no_cache") {
    	set $skip_cache 1;	}



	The variable "$skip_cache" could be different on your server. Make sure you check what is being used.



2. Then you should modify the server cache so it checks for that header. It will appear on the variable 			$upstream_http_wph_no_cache
	For redis cache configuration, look for the directives:

		srcache_fetch_skip $skip_cache;
		srcache_store_skip $skip_cache;

	And replace that with 

		srcache_fetch_skip $skip_cache$upstream_http_wph_no_cache;
		srcache_store_skip $skip_cache$upstream_http_wph_no_cache;

	For FastCGI, look for:

		proxy_cache_bypass $skip_cache;
		proxy_no_cache $skip_cache;



	And replace them with 

		proxy_cache_bypass $skip_cache$upstream_http_wph_no_cache;
		proxy_no_cache $skip_cache$upstream_http_wph_no_cache;

	If you already have a variable on that directive, then you can append the new one and it will work just fine. 

The exact code can be different for your server. 
If you are not sure how to setup this on your server, contact your hosting provider support team, they should be able to help you. 



## Frequently Asked Questions

### Where are the settings of the plugin?
There is no settings page. This plugin is designed to be small and simple.

## Help, Support and Contact

We only provide full support for this plugin to our [Managed WordPress](https://wpherc.com/) clients. 
If you find a bug you can create an issue on Github, or if you need any help to do your WordPress Maintenance, you can [contact us here](https://wpherc.com/contact/).

If you want us to help you to manage the server configuration of your clients (and much more), please check our [WPHercules partner program](https://wpherc.com/partners/).


## References and inspiration

I was inspired and get some code from the following sources.

We have used some code from the plugin *[https://wordpress.org/plugins/slim-maintenance-mode/](https://wordpress.org/plugins/slim-maintenance-mode/)* by Johannes [@wpdocde](https://profiles.wordpress.org/wpdocde/)
and also from *[https://wordpress.org/plugins/fresh-forms-for-gravity/](https://wordpress.org/plugins/fresh-forms-for-gravity/)* by Samuel Aguilera [@samuelaguilera](https://profiles.wordpress.org/samuelaguilera/)

Thank you specially to the [Gridpane](https://gridpane.com) support team for helping me during my tests and for giving me the last hint to crack this. 

Other links and texts to read that I have used:

- https://www.php.net/manual/en/function.setcookie.php
- https://cartflows.com/docs/allow-cache-plugins-to-cache-cartflows-pages/
- https://stackoverflow.com/questions/10763466/set-cookies-for-one-page-only-not-send-back-to-server-if-user-browse-other-page
- https://openresty.org/download/agentzh-nginx-tutorials-en.html#02-nginxdirectiveexecorder01
- https://www.linode.com/community/questions/5148/solved-nginx-fastcgi-caching
- https://www.digitalocean.com/community/tutorials/understanding-nginx-server-and-location-block-selection-algorithms
- https://www.ruby-forum.com/t/proxy-pass-location-inheritance/239135/2

Thank you all for your help.

## Future Developments

* Add filters to allow to change the cookie name.
* Add filters to allow to change the header name. 
* Add an automated clear Redis object cache.
* Add support for varnish (probably not happening). 
* Add support for other providers.
* Add support to clear Cloudflare cache.
