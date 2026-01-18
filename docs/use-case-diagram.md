```mermaid
useCaseDiagram
    title Enterprise Subscription Billing System

    actor Admin
    actor Customer
    actor System

    rectangle "System Boundary" {
        Admin -- (Manage Users)
        Admin -- (Manage Products & Pricing)
        Admin -- (Manage Customer Subscriptions)
        Admin -- (View Billing Reports)
        Admin -- (Configure System Settings)
        Admin -- (Handle Support Requests)

        Customer -- (View Dashboard)
        Customer -- (View & Pay Invoices)
        (View & Pay Invoices) .> (Process Payments) : <<extends>>

        Customer -- (Manage Own Subscription)
        (Manage Own Subscription) <|-- (Manage Customer Subscriptions)

        Customer -- (Submit Support Request)
        (Submit Support Request) <|-- (Handle Support Requests)

        System -- (Generate Invoices)
        (Generate Invoices) ..> (Send Notifications) : <<includes>>
        System -- (Process Payments)
        System -- (Send Notifications)
    }
```

**Explanation of Relationships:**

*   `-->`: Association (an actor invokes a use case)
*   `..>`: Include (a use case includes functionality of another)
*   `.>`: Extend (a use case may be extended by another)
*   `<|--`: Generalization (a more specific use case inherits from a general one)

This diagram provides a high-level overview of the system's functionality and how different roles interact with it. You can view this file with any Markdown viewer that supports Mermaid diagrams (like the GitHub web interface or various IDE extensions).
