#!/usr/bin/perl
use strict;
use CGI;
use CGI::Cookie;
require "common.pl";
my $cgi=new CGI;
my $cookie	=fetch CGI::Cookie
print "Content-type:text/html\n\n";
my $id='';
if(!$id){$id=$cookie{'id'}->value;}
if(!$id){$id=$cgi->param('id');}

my $session=();
if($id){$session= new session($id);}
if($id){&show_data()};
else{print "hi!";}

sub show_data
{
	my $username	=$session::username;
	my @feeds	=$session::feedlist;
	

}

