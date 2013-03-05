#!/usr/bin/perl
use strict;
use XML::FeedPP;
use DBI;
use CGI;
use Digest::SHA1 qw(sha1 sha1_hex sha1_base64);
`ps x | awk '{print $1}' | grep -v PID | xargs kill`;

my $cgi=new CGI;
my $dbh=DBI->connect("DBI:mysql:<dbname>:<hostname>","<username>","<password>");
my %namehash=();
my %titlehash=();
my %excludehash=();

my ($second, $minute, $hour, $dayOfMonth, $month, 
        $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings) = localtime();
        $yearOffset = 1900 + $yearOffset;
        $month++;$month='0'.$month if length($month)<2;
        $dayOfMonth='0'.$dayOfMonth if length($month)<2;
        my $today_time="$yearOffset-$month-$dayOfMonth $hour:$minute:$second";

my %feed_list=&get_feedlist();
my $count=0;
my @feed_ids=keys(%feed_list);
&shuffle(\@feed_ids);
foreach my $id(@feed_ids)
{
        $count++;
        &update_feed($id,$feed_list{$id},$namehash{$id});
        $dbh->do("analyze table ttrss_entries;") if $count %10 ==0;
}
exit();

sub update_feed
{
        my $id=shift            || return;
        my $source=shift        || return;
        my $username=shift;
        my $feed;
        my $op= "XML::FeedPP->new(\"$source\");";
        my @excludes=split(',',$excludehash{$id});
        $feed=eval($op);
        if($@){print "Error for $id ($source):$@\n";return;}
        if(!$feed){print "Feed is null\n";return;}
        print "\nTitle: ". $feed->title()."\n";
        print "ID: $id\n";
        print "Date: ". $feed->pubDate(). "\n";
        my %existing_entries=&get_existing_entries($id);
        print scalar(keys %existing_entries)." existing entries\n";
        print "Image: ". $feed->image()."\n";
        my $sql=qq{insert into ttrss_entries 
                (title,guid,link,updated,content,content_hash,
                feed_id,comments,no_orig_date,date_entered,user_name)
                values(?,?,?,?,?,?,?,?,?,NOW(),?)};
        my $sth=$dbh->prepare($sql);
        foreach my $item( $feed->get_item() )
        {
                my $url         =&escape($item->link()) || next;
                my $guid        =&escape($item->guid()) || $url;
                next if $existing_entries{$guid};
                my $title       =&escape($item->title()) || $feed->title();
                $title          =~s/\n//g;
                if(grep {$title=~m/$_/i } @excludes ){print "skipping $title\n";next;}   #skip items that match the exclude array
                my $desc        =&escape($item->description());
                my $desc_hash   =sha1_hex($desc);
                $desc           ='&nbsp;' if !$desc;
                my $updated     =$item->pubDate() || $today_time;
                my $comments    =&escape($item->get('comments')) || '&nbsp;';
                $sth->execute($title,$guid,$url,$updated,$desc,$desc_hash,
                        $id,$comments,'true',$username);
                print "+";
        }
        print "\n";
}
sub set_title
{
        my $id=shift;
        my $title=shift;
        if(!$id or !$title){return;}
        my $sql="update ttrss_feeds set title='$title' where id='$id' limit 1";
        my $sth=$dbh->prepare($sql);
        $sth->execute();
}
sub get_existing_entries
{
        my $id=shift || return;
        my $sql="select guid from ttrss_entries where feed_id='$id'";
        my $sth=$dbh->prepare($sql);
        $sth->execute();
        my %existing=();
        while(my $id=($sth->fetchrow_array())[0])
        {
                $existing{$id}=1;
        }
        return %existing;
}
sub get_feedlist
{
        my %rethash=();
        my $where='1';
        if($cgi->param('feed_id')){$where = " id = ".$cgi->param('feed_id')." ";}
        my $sql=qq{select id, feed_url,user_name,title,exclude from ttrss_feeds where $where};
        my $sth=$dbh->prepare($sql);
        $sth->execute();
        while(my ($id,$link,$username,$title,$exclude)=$sth->fetchrow_array)
        {
                $link=~s/http:\/\///;
                $link='http://'.$link;
                $rethash{$id}=$link;
                $namehash{$id}=$username;
                $titlehash{$id}=$title;
                $excludehash{$id}=$exclude;
        }
        return %rethash;
}

sub escape
{
        my $in=shift;
        $in=~s/'/&#39;/g;
        $in=~s/"/&#34;/g;
        $in=~s/\*/&#42;/g;
        $in=~s/\//&#47;/g;
        $in=~s/\\/&#92;/g;
        return $in;
}
sub shuffle
{
        my $array = shift;
        my $i = @$array;
        while ( --$i )
        {
                my $j = int rand( $i+1 );
                @$array[$i,$j] = @$array[$j,$i];
        }
}
