# bms_api
Restful API for bidding_management_system

### Endpoints
This API is currently in the development stage and the following endpoints might change in the future without prior notice.
For comments and suggestions, please do not hesitate to [submit](https://github.com/SEARCAPhil/bms_api/issues) an issue.

#### Suppliers
- **List**	`GET`	-	`/api/suppliers/`
- **List with status filter**	`GET`	-	`/api/suppliers/?status=blocked`
- **Profile**	`GET`	-	`/api/suppliers/?id=1`   


#### Suppliers \ Products
- **List**	`GET`	-	`/api/suppliers/products/?cid=1&cat=1`   
- **Preview**	`GET`	-	`/api/suppliers/products/?id=1`   
- **Search**	`GET`	-	`/api/suppliers/products/?param=HP`         


#### Suppliers \ Products \ Categories
- **List**	`GET`	-	`/api/suppliers/products/categories/?cid=1`
- **Includes sub-categories**	`GET`	-	`/api/suppliers/products/categories/?cid=1&sub=true`
- **Includes products**	`GET`	-	`/api/suppliers/products/categories/?cid=1&sub=true&prod=true`

> `Includes` have a `LIMIT` defined by the parent class. If you need more data, use the `Suppliers \ Products` namespace instead.   


#### Suppliers \ Products \ Templates
- **List**	`GET`	-	`/api/suppliers/products/templates/`
- **Preview**	`GET`	-	`/api/suppliers/products/templates/?id=1`   



#### Suppliers \ Accounts
- **List**	`GET`	-	`/api/suppliers/accounts/?cid=1`
- **Preview**	`GET`	-	`/api/suppliers/accounts/?id=1`
- **Domain search**	`GET`	-	`/api/suppliers/accounts/?param=searca.org&cid=1`
- **General search**	`GET`	-	`/api/suppliers/accounts/?param=searca.org`   

> Domain searching is not available in non-admin accounts      



#### Suppliers \ Accounts \ Logs
- **List**	`GET`	-	`api/suppliers/accounts/logs/?acc=1`	
- **List with event filter**	`GET`	-	`api/suppliers/accounts/logs/?acc=1&event=warnings`      


>`search` and `list` endpoints could have an additional `page` parameter   
Example :	`/api/suppliers/products/categories/?cid=1&page=2`