#!/usr/bin/perl

my $dbh=DBI->connect("DBI:mysql:ttrss:localhost","mobile","m0b1l3r0x");
sub dbh
{
        use DBI;
        return $dbh;
}

sub newsessionname
{
	my $filename='dict.txt';
	open(F,$filename);
	my @lines=<F>;
	close(F);
	#$index=rand @lines;
	#return $lines[$index];
	&fisher_yates_shuffle(\@lines);	
	my $element=shift @lines;
	while(&session_exists($element)){$element=shift @lines;}
	return $element;
}

sub fisher_yates_shuffle {
    my $array = shift;
    my $i;
    for ($i = @$array; --$i; ) {
        my $j = int rand ($i+1);
        next if $i == $j;
        @$array[$i,$j] = @$array[$j,$i];
    }
}

sub get_feed_list {
	my $dbh=&dbh();
	my $sql=qq{select id,title,feed_url,icon_url from ttrss_feeds where user_name='' or public='1'};
	my $sth=$dbh->prepare($sql);
	$sth->execute();
	my %rethash=();
	while(my @bob=$sth->fetchrow_array())
	{
		my $id=$bob[0];
		$rethash{$id}{'title'}=$bob[1];
		$rethash{$id}{'url'}=$bob[2];
		$rethash{$id}{'icon_url'}=$bob[3];
	}
	return %rethash;
}
sub session_exists
{
	my $dbh=&dbh();
	my $id=shift;
	my $sql=qq{select id from ttrss_sessions where name='$id'};
	        my $sth=$dbh->prepare($sql);
        $sth->execute();
        my %rethash=();
        while(my @bob=$sth->fetchrow_array())
        {
		if(@bob){return 1;}
	}
	return 0;
}
sub add_new_feed
{
	my $dbh=&dbh();
	my $feed_link=shift;
	my $feed_label=shift;
	if(!$feed_link or !$feed_label){ die "Feed or label missing<br>\n";}
	my $sql=qq{insert into ttrss_feeds (feed_url, title, public) 
		values ('$feed_link','$feed_label','1')};
	my $sth=$dbh->prepare($sql);
	my $sql2=qq{select LAST_INSERT_ID()};
	my $sth2=$dbh->prepare($sql2);
	$sth->execute();
	$sth2->execute();
	while(my @bob=$sth2->fetchrow_array()){return $bob[0];}
}
sub save_feedlist
{
	my $id=shift;
	my $list_ref=shift;my @feed_ids=@$list_ref;
	if(!$id or !$list_ref){die "Id or listref missing<br>\n";}
	my $sql=qq{insert into ttrss_session_feeds (feed_id, session_name) values (?,?)};
	my $sth=$dbh->prepare($sql);
	foreach my $feed_id(@feed_ids)
	{
		$sth->execute($feed_id,$id);
	}
}
sub get_feeds_by_name
{
	my $id=shift;
	my @feeds=();
	if(!$id){die "no id passed<br>\n";}
	my $sql=qq{select distinct feed_id from ttrss_session_feeds where session_name='$id'};
	my $sth=$dbh->prepare($sql);
	$sth->execute();
	while(my @bob=$sth->fetchrow_array())
	{
		push(@feeds,$bob[0]);
	}
	return @feeds;
}
1;


