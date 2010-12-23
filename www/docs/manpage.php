<?php
include('top.inc');
?>

<p><a name="__index__"></a></p>
<!-- INDEX BEGIN -->

<ul>

	<li><a href="#name">NAME</a></li>
	<li><a href="#synopsis">SYNOPSIS</a></li>
	<li><a href="#description">DESCRIPTION</a></li>
	<li><a href="#nodes">NODES</a></li>
	<li><a href="#options">OPTIONS</a></li>
	<li><a href="#central_database">CENTRAL DATABASE</a></li>
	<li><a href="#file_uploading">FILE UPLOADING</a></li>
	<li><a href="#examples">EXAMPLES</a></li>
	<li><a href="#see_also">SEE ALSO</a></li>
	<li><a href="#bugs">BUGS</a></li>
	<li><a href="#author">AUTHOR</a></li>
</ul>
<!-- INDEX END -->

<hr />
<p>
</p>
<h1><a name="name">NAME</a></h1>
<p>parsh - Parallel SSH client</p>
<p>
</p>
<hr />
<h1><a name="synopsis">SYNOPSIS</a></h1>
<p>parsh [-1Qhqrsv] [--nodetach] [-e rate] [-c command] [-j jobs] [-l user]
[-u source:destination] node [node [...]]</p>
<p>
</p>
<hr />
<h1><a name="description">DESCRIPTION</a></h1>
<p>parsh is a program for executing commands on remote machines in parallel. One
benefit of parsh over traditional for i in ... loop implementations is that
parsh captures STDOUT, STDERR and the exit status of the remote command. In
addition everything is logged to a central database. See the <a href="#central_database">CENTRAL DATABASE</a>
section for more information.</p>
<p>
</p>
<hr />
<h1><a name="nodes">NODES</a></h1>
<p>Any combination of nodes, nodegroups and nodegroup expressions may be given on
the command line.</p>
<p>
</p>
<hr />
<h1><a name="options">OPTIONS</a></h1>
<dl>
<dt><strong><a name="item__2d1"><strong>-1</strong></a></strong>

<dd>
<p>Normally parsh will prompt for your corporate realm credentials and the realm
credentials of the target node. This option instructs parsh to to use only your
corporate credentials.</p>
</dd>
</li>
<dt><strong><a name="item__2dq"><strong>-Q</strong></a></strong>

<dd>
<p>Only print fatal errors. This option is meaningless unless <strong>--nodetach</strong> is also
given.</p>
</dd>
</li>
<dt><strong><a name="item__2dc_command_2c__2d_2dcommand_command"><strong>-c</strong> <em>command</em>, <strong>--command</strong> <em>command</em></a></strong>

<dd>
<p>The command to execute on remote machines. Be aware of your shell's quoting rules.
May be used multiple times.</p>
</dd>
</li>
<dt><strong><a name="item__2de_number_7cpercentage"><strong>-e</strong> <em>number</em>|<em>percentage</em></a></strong>

<dd>
<p>The number of failures to allow before abandoning the job. This can be either
a whole number or a percentage. After this many nodes have failed, parsh will not
run against any more nodes. The default is to stop after 10% failures.</p>
</dd>
</li>
<dt><strong><a name="item__2dh_2c__2d_2dhelp"><strong>-h</strong>, <strong>--help</strong></a></strong>

<dd>
<p>Display a short help message.</p>
</dd>
</li>
<dt><strong><a name="item__2dj_jobs_2c__2d_2djobs_jobs"><strong>-j</strong> <em>jobs</em>, <strong>--jobs</strong> <em>jobs</em></a></strong>

<dd>
<p>How many remote machines to connect to at once. The default is 10.</p>
</dd>
</li>
<dt><strong><a name="item__2dl_user_2c__2d_2dlogin_user"><strong>-l</strong> <em>user</em>, <strong>--login</strong> <em>user</em></a></strong>

<dd>
<p>Login as <em>user</em>. Use this option if you need to login to the remote machines as a
different user.</p>
</dd>
</li>
<dt><strong><a name="item__2dq_2c__2d_2dquiet"><strong>-q</strong>, <strong>--quiet</strong></a></strong>

<dd>
<p>Only print warnings and errors. This option is meaningless unless <strong>--nodetach</strong> is
also given.</p>
</dd>
</li>
<dt><strong><a name="item__2dr_2c__2d_2droot"><strong>-r</strong>, <strong>--root</strong></a></strong>

<dd>
<p>This option will cause parsh to watch for password prompts ('Password:')
on STDOUT. The root password will be prompted for upon invocation. When a prompt
is encountered, the root password requested earlier will be sent to the prompt.</p>
</dd>
</li>
<dt><strong><a name="item__2ds_2c__2d_2dsudo"><strong>-s</strong>, <strong>--sudo</strong></a></strong>

<dd>
<p>This option will cause parsh to watch for password prompts ('Password:')
on STDERR. When a prompt is encountered, the password requested earlier will be
sent to the prompt.</p>
</dd>
</li>
<dt><strong><a name="item__2du_source_3adestination_2c__2d_2dupload_source_3"><strong>-u</strong> <em>source:destination</em>, <strong>--upload</strong> <em>source:destination</em></a></strong>

<dd>
<p>Use this option to upload files before the command is executed. Multiple files
can be uploaded by specifying this opton multiple times. See the <a href="#file_uploading">FILE UPLOADING</a>
section for more information including caveats.</p>
</dd>
</li>
<dt><strong><a name="item__2dv_2c__2d_2dverbose"><strong>-v</strong>, <strong>--verbose</strong></a></strong>

<dd>
<p>Increase verbosity. May be used multiple times. This option is meaningless unless
&lt;B--nodetach&gt; is also given.</p>
</dd>
</li>
</dl>
<p>
</p>
<hr />
<h1><a name="central_database">CENTRAL DATABASE</a></h1>
<p>Upon successful invocation, parsh will request a job ID from the central database.
Stored in the database will be the command, the 'run as' user, any files
to be uploaded, the nodes to run on and the start time.</p>
<p>As parsh connects to each remote machine, an entry will be added to the central
database with the job ID, node and start time. As each node finishes, the
exit status, STDOUT and STDERR, and finish time will be appended to the entry.
If parsh was not able to connect to the remote machine, the exit status will be -1 and
the entry will contain an error message indicating the failure. If parsh was able
to connect to the remote machine, then the exit status will be the exit status
from the last command or the last uploaded file if no command was given. If any file
fails to upload or any command exits non-zero, parsh will not continue with that node.</p>
<p>After all remote machines have been acted upon, the finish time of the job will be
recorded in the database.</p>
<p>
</p>
<hr />
<h1><a name="file_uploading">FILE UPLOADING</a></h1>
<p>Parsh's file uploading is trivial at best. Only files are supported at this time.
Permissions on the source file will be applied to the destination. In basic terms,
this is what happens to upload a file:</p>
<pre>
  cat $source | ssh $node &quot;cat - &gt; $destination&quot;
  ssh $node &quot;chmod #### $destination&quot;</pre>
<p>
</p>
<hr />
<h1><a name="examples">EXAMPLES</a></h1>
<p>Run the command <code>ls /tmp</code> on util1.sjc1 and util1.bom1:</p>
<pre>
  parsh -c 'ls /tmp' util1.sjc1 util1.bom1</pre>
<p>Run the command <code>w</code> on all db class servers in sjc1:</p>
<pre>
  parsh -c 'ls /tmp' '&amp;intersect(@nodes.model_id.10,@nodes.site_id.1)'</pre>
<p>Upload the file <em>foo</em> to <em>/tmp/foo</em> on helios1.sjc1:</p>
<pre>
  parsh -u foo:/tmp/foo helios1.sjc1</pre>
<p>Upload the file <em>foo.sh</em> and then execute it:</p>
<pre>
  parsh -u foo.sh:/tmp/foo.sh -c '/tmp/foo.sh'</pre>
<p>
</p>
<hr />
<h1><a name="see_also">SEE ALSO</a></h1>
<p><strong>ssh</strong>(1), <strong>sudo</strong>(8)</p>
<p>
</p>
<hr />
<h1><a name="bugs">BUGS</a></h1>
<p>Normally parsh will detach from the current terminal. This is not the case
if <strong>-r</strong> is given.</p>
<p>
</p>
<hr />
<h1><a name="author">AUTHOR</a></h1>
<p>Kimo Rosenbaum &lt;<a href="mailto:kimor79@yahoo.com">kimor79@yahoo.com</a>&gt;</p>

</body>

</html>
