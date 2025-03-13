export default [
    {
        path: "/web-settings/general",
        name: "web-settings-general",
        component: () => import("@/views/pages/web-settings/General.vue"),
        meta: {
            pageTitle: "",
            breadcrumb: [
                {
                    text: "Web Settings",
                    active: false,
                },
                {
                    text: "General",
                    active: true,
                },
            ],
        },
    },
    {
        path: "/web-settings/privacy-policy",
        name: "privacy-policy-page",
        component: () => import("@/views/pages/web-settings/PrivacyPolicy.vue"),
        meta: {
            pageTitle: "",
            breadcrumb: [
                {
                    text: "Web Settings",
                    active: false,
                },
                {
                    text: "Privacy Policy",
                    active: true,
                },
            ],
        },
    },
    {
        path: "/web-settings/index-page",
        name: "web-settings-index-page",
        component: () => import("@/views/pages/web-settings/IndexPage.vue"),
        meta: {
            pageTitle: "",
            breadcrumb: [
                {
                    text: "Web Settings",
                    active: false,
                },
                {
                    text: "Index Page",
                    active: true,
                },
            ],
        },
    },
];
