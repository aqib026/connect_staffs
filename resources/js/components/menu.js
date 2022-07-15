export const menuItems = [
    {
        id: 1,
        label: "menuitems.menu.text",
        isTitle: true
    },
    {
        id: 2,
        label: "Manage Users",
        icon: "bx-home-circle",
        badge: {
            variant: "info",
            text: "menuitems.dashboards.badge"
        },
        subItems: [
            {
                id: 3,
                label: "Manage Administrators",
                link: "/admins",
                parentId: 2
            },
            {
                id: 4,
                label: "Manage Users",
                link: "/dashboard/saas",
                parentId: 2
            },
            {
                id: 5,
                label: "menuitems.dashboards.list.crypto",
                link: "/dashboard/crypto",
                parentId: 2
            },
            {
                // id: 5,
                label: "menuitems.dashboards.list.blog",
                link: "/dashboard/blog",
                parentId: 2
            }
        ]
    },
    {
        id: 6,
        isLayout: true
    },
    {
        id: 7,
        label: "menuitems.apps.text",
        isTitle: true
    },
    {
        id: 8,
        label: "menuitems.calendar.text",
        icon: "bx-calendar",
        link: "/calendar"
    },
    {
        id: 9,
        label: "menuitems.chat.text",
        icon: "bx-chat",
        link: "/chat"
    },
    {
        id: 11,
        label: "menuitems.filemanager.text",
        link: "/file-manager",
        icon: "bx-file",
        badge: {
            variant: "success",
            text: "menuitems.chat.badge"
        }
    },
    {
        id: 10,
        label: "menuitems.ecommerce.text",
        icon: "bx-store",
        subItems: [
            {
                id: 11,
                label: "menuitems.ecommerce.list.products",
                link: "/ecommerce/products",
                parentId: 10
            },
            {
                id: 12,
                label: "menuitems.ecommerce.list.productdetail",
                link: "/ecommerce/product-detail",
                parentId: 10
            },
            {
                id: 13,
                label: "menuitems.ecommerce.list.orders",
                link: "/ecommerce/orders",
                parentId: 10
            },
            {
                id: 14,
                label: "menuitems.ecommerce.list.customers",
                link: "/ecommerce/customers",
                parentId: 10
            },
            {
                id: 15,
                label: "menuitems.ecommerce.list.cart",
                link: "/ecommerce/cart",
                parentId: 10
            },
            {
                id: 16,
                label: "menuitems.ecommerce.list.checkout",
                link: "/ecommerce/checkout",
                parentId: 10
            },
            {
                id: 17,
                label: "menuitems.ecommerce.list.shops",
                link: "/ecommerce/shops",
                parentId: 10
            },
            {
                id: 18,
                label: "menuitems.ecommerce.list.addproduct",
                link: "/ecommerce/add-product",
                parentId: 10
            }
        ]
    },

    {
        id: 60,
        label: "menuitems.components.text",
        isTitle: true
    },



];
