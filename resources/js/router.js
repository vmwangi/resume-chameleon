import { createRouter, createWebHistory } from "vue-router"
import Home from "./components/Home.vue"
import Pricing from "./components/Pricing.vue"
import Login from "./components/Login.vue"
import Dashboard from "./components/Dashboard.vue"
import ResumeCreate from "./components/ResumeCreate.vue"
import ResumeView from "./components/ResumeView.vue"
import Settings from "./components/Settings.vue"
import Terms from "./components/Terms.vue"
import Privacy from "./components/Privacy.vue"
import Contact from "./components/Contact.vue"
import Authenticated from "./components/Authentication.vue"
import Resumes from "./components/Resumes.vue"
import NotFound from "./components/NotFound.vue"

const routes = [
  {
    path: "/",
    component: Home,
    meta: { requiresAuth: false },
  },
  {
    path: "/pricing",
    component: Pricing,
    meta: { requiresAuth: false },
  },
  {
    path: "/login",
    component: Login,
    meta: { requiresAuth: false },
  },
  {
    path: "/authenticated",
    component: Authenticated,
    meta: { requiresAuth: false },
  },
  {
    path: "/dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: "/dashboard/resume/create",
    component: ResumeCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/dashboard/resume/:id",
    component: ResumeView,
    meta: { requiresAuth: true },
  },
  {
    path: "/resumes",
    component: Resumes,
    meta: { requiresAuth: true },
  },
  {
    path: "/terms",
    component: Terms,
    meta: { requiresAuth: false },
  },
  {
    path: "/privacy",
    component: Privacy,
    meta: { requiresAuth: false },
  },
  {
    path: "/contact",
    component: Contact,
    meta: { requiresAuth: false },
  },
  {
    path: "/settings",
    component: Settings,
    meta: { requiresAuth: true },
  },
  // Add the 404 catch-all route at the end
  {
    path: "/:pathMatch(.*)*",
    component: NotFound,
    meta: { requiresAuth: false },
  },
]

const router = createRouter({
  history: createWebHistory("/"),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Always scroll to top when navigating to a new page
    return { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("auth_token")

  // Handle the authenticated callback route
  if (to.path === "/authenticated") {
    const urlToken = to.query.token
    if (urlToken) {
      localStorage.setItem("auth_token", urlToken)
      next("/dashboard")
      return
    }
  }

  // Check for protected routes
  if (to.meta.requiresAuth) {
    if (token) { 
      next()
    } else {
      next("/login")
    }
  } else {
    // Redirect authenticated users away from login/landing pages
    if (token && (to.path === "/login" || to.path === "/")) {
      next("/dashboard")
    } else {
      next()
    }
  }
})

export default router