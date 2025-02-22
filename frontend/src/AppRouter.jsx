import { lazy, Suspense } from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import NotFound from '@/pages/common/NotFound.jsx'

export default function AppRouter() {
  const Home = lazy(() => import('@/pages/guest/Home.jsx'))
  const Posts = lazy(() => import('@/pages/guest/Posts.jsx'))
  const Login = lazy(() => import('@/pages/guest/Login.jsx'))
  const Register = lazy(() => import('@/pages/guest/Register.jsx'))
  const NotFound = lazy(() => import('@/pages/common/NotFound.jsx'))
  return (
    <BrowserRouter>
      <Suspense fallback={null}>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/posts" element={<Posts />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="*" element={<NotFound />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  )
}
