import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import AppRouter from '@/AppRouter.jsx'
import AppLayout from '@/layouts/common/AppLayout.jsx'
import { axios } from '@/utils/axios.js'

export const fetcher = (url) => axios.get(url).then((res) => res.data)

export default function App() {
  return (
    <AppLayout>
      <AppRouter />
    </AppLayout>
  )
}
