=== Creative Commons Media Tagger ===  
Contributors: CodeAndReload  
Donate link: http://www.codeandreload.com/wp-plugins/creative-commons-tagger/#donate  
Tags: creative commons, license, cc, cc-rel, RDFa, metadata, open source, media, licensing, upload, photo, attachment  
Requires at least: 2.9  
Tested up to: 3.0.1  
Stable tag: 2.2


Allows tagging of media as having a Creative Commons license. License info shows as link and/or image and is searchable. Search engine optimized.


== Description ==

This plugin provides the ability to tag media in the media library as having a Creative Commons (CC) license.
The license shows up on the attachment page and is optimized for search engines (SEO) using RDFa metadata.
It optionally extends the search form to allow searches for CC-tagged media.  It can display a text link
to the license, an image link to the license, or both.


== Installation ==

Installation is simple and straight-forward:

1. Unzip `cc-tagger.zip` into to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure the plugin options under the 'Settings->Media' menu in WordPress. Don't forget to Save Changes!
1. Run CC-Tag Batch Index (Tools->cc-tagger batch index) to tag media that was already in your
media library prior to installing and activating CC-Tagger.


== Frequently Asked Questions ==

= What is Creative Commons? =

Creative Commons licenses are several copyright licenses that allow the distribution of copyrighted works.
Additional information can be found at the [Creative Common's homepage](http://www.creativecommons.org/ "Creative Commons homepage").

= What will be shown when searching for CC-tagged media? =

When running a search for CC-tagged media, it will show the media *even if it is attached to a post that
is not published*.

= Why is CC license information being displayed only on new media? =

You should run CC-Tag Batch Index (Tools->cc-tagger batch index) to tag media that was already in your
media library prior to installing and activating CC-Tagger.

= Why does my site "lock up" when I run CC-Tag Batch Index? =

Your site has most likely not actually "locked up."  It takes a while for CC-Tag Batch Index to go through
all the media and apply the tags.  If you have a very large number of media, that time might be very long
indeed.  You can run CC-Tag Batch Index on a smaller set of media by entering starting and ending IDs,
effectively processing the media in smaller batches, resulting in a shorter-term load on your site.

Note that running CC-Tag Batch Index should normally be a one-time process (when you first install and
activate CC-Tagger).  After that, you can apply the CC information to each media item as it is uploaded.

= How can I change the CC info for a range of attachments in the media library? =

CC-Tag Batch Index may be used to tag a range of media in the media library that is either not
yet tagged, or to change the existing tags in a range of attachment IDs.

To tag all media in that range whether or not it is already tagged, you should check the "overwrite
existing license data" option.

= What new options are there for uploaded media? =

When new media is uploaded, two additional fields are provided for selecting CC Usage Rights and CC
Modification Rights. Select the desired setting for that media in the dropdown boxes.

= What are the options for CC-Tagger and where do I set them? =

All the CC-Tagger initial options are set under the 'Settings->Media' menu. There you will find options
for

* **Jurisdiction of your license information** - This allows you to set the CC jurisdiction of your media
so that it applies to only one country or region, or internationally.
* **Badge size** - This sets the size of the CC "badge," if any, displayed along with the media.
* **Display a text link next to the license** - This determines whether or not a text link will be
displayed (either with or instead of the badge).
* **By Default allowed use of untagged media** - This setting will be used for any uploaded media
set to 'default.'
* **By Default allowed modification of untagged media** - This setting will be used for any uploaded
media set to 'default.'
* **Add search options to the search form** - If set to Yes, it will allow your site visitors to search
for CC-tagged media from the search form by providing options to search based on usage and/or modification
rights.

== Screenshots ==

1. This is a sample image attachment page showing the Creative Commons license information displayed.
1. This is the options screen showing the settings available with CC-Tagger.
1. This is the CC-Tagger Batch Index screen.
1. This is the search form with the CC options added (if enabled) by CC-Tagger.
1. This is a '404 - Not found' page result showing the search option with the CC options added (if enabled) by CC-Tagger.

== Changelog ==

= 1.5 =  
* Initial public release.
= 1.7 =  
* Fixed an error due toa a stray equal-sign missing.



== Upgrade Notice ==

= 1.5 =  
* Initial public release.


== Support ==

Technical support for this plugin will be provided via the WordPress plugin forum.  Additional support may be
available at [plugin's homepage](http://www.codeandreload.com/wp-plugins/creative-commons-tagger/ "Creative Commons Media
Tagger at Code and Reload").
