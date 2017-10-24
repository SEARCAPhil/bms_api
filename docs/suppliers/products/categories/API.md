# Suppliers \ Products \ Categories
- **List**	`GET`	-	`/api/suppliers/products/categories/?cid=1`
- **Includes sub-categories**	`GET`	-	`/api/suppliers/products/categories/?cid=1&sub=true`
- **Includes products**	`GET`	-	`/api/suppliers/products/categories/?cid=1&sub=true&prod=true`

> `Includes` have a `LIMIT` defined by the parent class. If you need more data, use the `Suppliers \ Products` namespace instead.