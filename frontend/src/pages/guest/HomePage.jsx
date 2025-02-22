import { Link } from 'react-router-dom'
import { axios } from '@/utils/axios.js'

export default function HomePage() {
  const handleLogout = async () => {
    try {
      const response = await axios.post('/api/auth/logout')
      alert(response.data.message)
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    } catch (e) {
      alert('로그인된 상태가 아닙니다.')
    }
  }
  return (
    <div className={'flex items-center justify-center h-screen flex-col gap-3'}>
      <Link to={'/register'}>회원가입</Link> <Link to={'/login'}>로그인</Link>{' '}
      <Link to={'/posts'}>게시판</Link>
      <button onClick={handleLogout}>로그아웃</button>
    </div>
  )
}
