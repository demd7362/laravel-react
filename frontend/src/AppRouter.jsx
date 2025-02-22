import { lazy, Suspense } from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import NotFoundPage from '@/pages/common/NotFoundPage.jsx'
import Loading from '@/components/Loading.jsx'

export default function AppRouter() {
  const Home = lazy(() => import('@/pages/guest/HomePage.jsx'))
  const Posts = lazy(() => import('@/pages/guest/PostsPage.jsx'))
  const Login = lazy(() => import('@/pages/guest/LoginPage.jsx'))
  const Register = lazy(() => import('@/pages/guest/RegisterPage.jsx'))
  const NotFound = lazy(() => import('@/pages/common/NotFoundPage.jsx'))
  const Post = lazy(() => import('@/pages/guest/PostPage.jsx'))
  const Write = lazy(() => import('@/pages/user/WritePage.jsx'))
  return (
    <BrowserRouter>
      <Suspense fallback={<Loading />}>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/posts" element={<Posts />} />
          <Route path="/posts/:postId" element={<Post />} />
          <Route path="/posts/write/:postId?" element={<Write />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="*" element={<NotFound />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  )
}
