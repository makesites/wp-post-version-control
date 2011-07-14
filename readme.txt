=== Post Version Control ===
Contributors: kdiweb
Donate link: http://www.makesites.cc/donate.php
Tags: version control, automatic, subversion, svn, revision, versioning
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 1.0

Automatic version control for posts with the same prefix in the post_name

== Description ==

Posts can be grouped in many ways in Wordpress but there is no direct version control system. This plugin offers a lightweight solution if you want version control for a group of posts. 

To make this work you define a keyword for each version control group you want to create. Then use that keyword as a prefix in the post name of each post in that group. The plugin will sort out the latest post automatically and label the rest as outdated. 

It also let's you have a URL that will always point to the latest post of that version control group. That way you can give out the URL to others and be sure that your visitors will always be viewing the most recent post. 

This plugin will automatically: 

*   Define if a post is part of a version control group. 
*   Search and locate the latest post by publish date.
*   Label the older posts of the group as outdated.

This is especially practical for bloggers that often revisit older subjects and have to do the linking with the updated posts manually. Or if you are like me and publish material that has versioning applied to it (ex. software) through a blogging platform.

== Installation ==

1. Upload the directory "post-version-control" into your wp-content/plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Open the plugin's options page, which is located under "Settings" -> "Post Version Control".
4. Define the keywords you want to track for your version control groups and the URL you want to use for the latest post.

More detailed information is provided in the "Usage" section of this document. 

== Frequently Asked Questions ==

= Why use this plugin? =

To mention some the benefits: 
* It's all done automatically. Make sure the post name starts with the keyword of the version control group and the rest is handled by the plugin. 
* It's very light weight. I doesn't modify the database structure and at worst will only do two extra database writes when you are saving a post.
* You can instantly change the styling of the outdated tag by modifying the "outdated.html" in the plugin's directory. 

= Any known limitations? =

When using the script you should know that: 
* The keyword should be the first part of the name which means that every post can belong to one version control group. 
* The URLs of the latest post should not be changed frequently as the links may be cached by your computer or ISP. That means the old links may still work for a short period of time. 
* The keyword cannot contain a dash itself as it is used as a delimiter for distinguishing them in the post names. 
* If a new post becomes part of a version control group by mistake (ex. when the title starts with the same word as a keyword), changing the post name to a different one does not take off the outdated tag from the previous post in line.  

= Need more help? Found a bug? Have an idea? =

Contact me at [Make Sites](http://www.makesites.cc/contact/ "This is yet another Make Sites production")

== Screenshots ==

== Usage ==

First thing you should do is visit the plugin's options page ("Settings" -> "Post Version Control"). 

There you will find two fields: 

1. The keyword list field, where you enter all the keywords you want to track seperated from each other by a comma and a space ", ". Each of them defines a different version control group.

2. The path for the latest posts URL. By default the path is "/current". So if we have a keyword named "ajax" the URL: http://www.myblogaddress.com/current/ajax will point to the latest post of the "ajax" group. 

To link a post to a version control group of posts all you need to do is enter the keyword of that group as a prefix to the post_name of that post. So in the group "ajax" mentioned earlier all posts in that group should start with the "ajax-" prefix. An example list of that group of posts might be: 

ajax-first-post
ajax-updated-version
ajax-latest-news

..and so on. 

The plugin will automatically look through your posts of the same group and label the older ones by inserting an &lt;!-- outdated --&gt; tag. This is replaced by the content of "outdated.html" in runtime. This is an example of how you can style your outdated tag. Just insert it along with your other styles: 

<style>
p.outdated{
  border: dashed 1px #f00;
  background: #eee;
  color: #f00;
  text-align: center;
}

p.outdated a {
  color: #f00;
}
</style>

Uninstalling should be pretty easy too. The only left overs are the <!-- outdated: ... --> comments in the post's content which of course are ignored when viewing the webpage. If you don't mind that you don't have to do anything else apart from de-activating the plugin through your Wordpress administration. 

== License ==

This work is released under the terms of the GNU General Public License:
http://www.gnu.org/licenses/gpl-2.0.txt
