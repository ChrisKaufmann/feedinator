#!/usr/bin/perl;
use strict;
use DBI;
use Digest::SHA1 qw(sha1 sha1_hex sha1_base64);
my $dbh=DBI->connect("DBI:mysql:<dbname>:<hostname>","<username>","<password>");
my $sql="select id,expirey from ttrss_feeds where expirey != '' and expirey is not null;";
my $sth=$dbh->prepare($sql);
$sth->execute();
while(my ($id,$expirey)=$sth->fetchrow_array)
{
	if(!$id){next;}
	print "Expiring from feed $id\n";
	my $sql2="delete from ttrss_entries where feed_id='$id' and  date_entered < date_sub(now(),interval $expirey) and marked=0";
	my $sth2=$dbh->prepare($sql2);
	$sth2->execute();
}
