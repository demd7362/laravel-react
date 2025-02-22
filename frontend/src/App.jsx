import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import AppRouter from '@/AppRouter.jsx'
import AppLayout from '@/layouts/common/AppLayout.jsx'

export default function App() {
  return (
    <AppLayout>
      <AppRouter />
    </AppLayout>
  )
}
