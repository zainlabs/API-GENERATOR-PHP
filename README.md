# API-GENERATOR-PHP
<b>API GENERATOR PHP</b>

<b>Get All</b>
<br>
<quote>
Example : <code>https://localhost/api.php</code>
</quote>

<b>Get By ID</b>
<br>
<quote>
Example : <code>https://localhost/api.php?id=1</code>
</quote>

<b>Get By Custom Field</b>
<br>
<quote>
Example : <code>http://localhost/api.php?table=user&type=equal&field=name&search=admin</code>
<br>
Example : <code>http://localhost/api.php?table=user&type=like&field=name&search=admin</code>
</quote>

<b>Get All with custom table</b>
<br>
<quote>
Example : <code>http://localhost/api.php?table=messages</code>
</quote>

<b>Get All with custom query</b>
<br>
<quote>
Example : <code>localhost/api.php?q=SELECT * FROM users WHERE username='admin'</code>
<br>
Don't forget to set <code>$query_type 	= 'manual';</code>
</quote>

<b>Insert Data (Method POST)</b>
<br>
<quote>
Example Base Url = <code>http://localhost/api.php</code>
</quote>

<b>Update Data (Method PUT with parameter id_update)</b>
<br>
<quote>
Example Base Url = <code>http://localhost/api.php</code>
</quote>

<b>Delete Data (Method DELETE with parameter id_delete)</b>
<br>
<quote>
Example Base Url = <code>http://localhost/api.php</code>
</quote>
