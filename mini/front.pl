#!/usr/bin/perl
use strict;
use CGI;
use CGI::Cookie;
require "common.pl";
my %cookie      =fetch CGI::Cookie;
my $cgi		=new 	CGI;
my $id='';
my $debug=1;
if(!$id && %cookie){$id=$cookie{'id'}->value;}
if(!$id){$id=$cgi->param('id');}
print "Content-type: text/html\n\n";

if(!$id){&show_main();}		#no existing, show new screen
else{&show_data();}
sub show_main
{
my %allfeeds=&get_feed_list;

print "<form methos=\"POST\">\n";
print "<table>\n<tr><td>ID</td><td>Title</td><td>Url</td><td>Icon</td></tr>\n";
foreach my $id(keys %allfeeds)
{
	print "<tr>	<td><input type=\"checkbox\" name='feed_$id' value='$id'></td>
			<td>".$allfeeds{$id}{'title'}."</td>
			<td>".$allfeeds{$id}{'url'}."</td>";
	if($allfeeds{$id}{'icon_url'}){print"<td><img src=\"".$allfeeds{$id}{'icon_url'}."\" height='15'></td>";}
	print"</tr>";
}


print <<cEnd;
</table>






<hr>
new feeds<tr>
<table>
	<tr><td></td><td>Label</td><td>Link</td></tr>
cEnd
for my $i(1..5)
{
	print <<cEnd
	<tr>
		<td>New feed $i:</td>
		<td><input type="text" name="new_label_$i" size="40"></td>
		<td><input type="text" name="new_link_$i" "size="60"></td>
	</tr>
cEnd
}
print <<cEnd;
<hr>
</table>
cEnd
my $temp_id=&newsessionname();
print "<hr>\nSession name.  Feel free to change to remember later.<br>\n";
print "<input type=\"text\" name=\"id\" value=\"$temp_id\">\n";
print "<hr>\n";
print "<input type=\"submit\">\n";

}  #end of sub show_main
sub show_data
{
	my @feed_ids=();
	my %new_feeds=();
	print "welcome $id<br><br>\n";
	print "<hr>\n";
        for my $i(1..5)
        {
               my $new_link=$cgi->param("new_link_$i");
               my $new_label=$cgi->param("new_label_$i");
               if($new_link && $new_label)
               {
                        print "$new_link - $new_label<br>\n" if $debug;
			my $feed_id=&add_new_feed($new_link,$new_label);
			push (@feed_ids,$feed_id);
               }
        }
	print "<br>\n"  if $debug;
	foreach my $parm($cgi->param)
	{
		print "$parm-".$cgi->param($parm)."<br>\n" if $debug;
		if($parm =~ /feed_/){push(@feed_ids,$cgi->param($parm));}
	}
	foreach my $id(@feed_ids)
	{
		print "id=$id...<br>\n" if $debug;
	}
	&save_feedlist($id,\@feed_ids);
	&print_feeds();
}
sub print_feeds
{
	my @feeds=&get_feeds_by_name($id);
	foreach my $feed_id(@feeds)
	{
		print "feedid=$feed_id<br>\n" if $debug;
	}
}
