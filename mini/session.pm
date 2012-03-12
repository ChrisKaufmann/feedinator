package session;
use strict;
require "common.pl";
my %data=();
my $dbh=&dbh();
my $exists=0;
sub new
{
	%data=();
	my $id=shift;
	if(!$id){ }  #new....
	else{$exists=1;}

)
sub username
{
	shift;
	my $new=shift;
	if($new){$data{'username'}=$new;}
	return $data{'username'};
}




