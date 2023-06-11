# Angular related assisments

## 1. Will give blank running ng10 app
- Will have predetermined child and parent component
- Pass an array of object from one component to another
- Display array of object on HTML page
- Display on 5 items from object to page as cards.
- Navigate through cards via tab.

  ```
  [
    {
      EmployeeName: “Person 1”,
      EmployeeId: “P1”,
      EmployeeDepartment: “Electrical”
    },
    {
      EmployeeName: “Person 2”,
      EmployeeId: “P2”,
      EmployeeDepartment: “Electrical”
    },
    {
      EmployeeName: “Person 3”,
      EmployeeId: “P3”,
      EmployeeDepartment: “Electrical”
    },
    {
      EmployeeName: “Person 4”,
      EmployeeId: “P4”,
      EmployeeDepartment: “Electrical”
    },
    {
      EmployeeName: “Person 5”,
      EmployeeId: “P5”,
      EmployeeDepartment: “Electrical”
    }
  ]
  ```

# Angular Tutorial Explaination

## Angular Service
- _Angular services_ provide a way to separate Angular app data and functions that can be used by multiple components in app.
- To be used by multiple components, a service must be made _injectable_.
- Services that are injectivle and used by a component becamme dependencies of that component.
- The component depends on those services and can't function without them.

**Dependency injection**

_Dependency injection_ is the mechanism that manages the dependencies of an app's components and the services that other components can use.

**To create a service:**

`ng generate service housing`