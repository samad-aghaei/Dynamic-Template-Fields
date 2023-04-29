# Dynamic Template Fields
Proof of concept for creating dynamic form fields for an HTML template file using native es6 sting template literals.

Traditionally, if we need to add or remove any of the input fields in the admin dashboard, we should change the database tables, admin files, validator files and the HTML template file/string.

Consider the following HTML+es6 string template literal template style code:
```
<table>
    <tbody>
	${ post.users.map( user => `
          <tr>
             <td>${ user.ID }</td>
             <td>${ user.firstname }</td>
             <td>${ user.lastname }</td>
             <td>${ user.register_date }</td>
          </tr>
	`)}
    </tbody>
</table>
 ```
By adding a new property or removing any of the user object properties in the template, the same changes would be applied to the admin area automatically.
Beside the meaningful syntax and readablity, we can control what we need to provide to the end user by changing our template file only.
Of-course we can to do the data validation and the database table modification based on those changes.
