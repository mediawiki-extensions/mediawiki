<?xml version="1.0" encoding="UTF-8" ?>
<f xmlns="http://pear.php.net/dtd/rest.categorypackageinfo"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xsi:schemaLocation="http://pear.php.net/dtd/rest.categorypackageinfo
    http://pear.php.net/dtd/rest.categorypackageinfo.xsd">
<pi>
	<p>
	 <!-- customize here -->
	 <c>jldupont.googlecode.com/svn</c>
	 <n>$package</n>
	 <!-- put category of extension here -->
	 <ca xlink:href="/rest/c/$category">$category</ca>
	 
	 <l>$license</l>
	 <s>$summary</s>
	 <d>$description</d>
	 
	 <!-- release REST directory -->
	 <r xlink:href="/rest/r/$package"/>
	</p>
	<a>
	 <!-- put latest release information here -->
	 <!-- This is the information that appears when doing list-all command -->	 
	 <r>
	 	<v>$version</v>
	 	<s>$stability</s>
 	</r>
	</a>
</pi>
</f>