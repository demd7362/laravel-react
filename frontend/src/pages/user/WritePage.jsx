import { Link, useNavigate, useParams, useSearchParams } from 'react-router-dom'
import { useEffect, useRef, useState } from 'react'
import useRefresh from '@/hooks/useRefresh.jsx'
import ToastUiEditor from '@/components/ToastUiEditor.jsx'
import { axios } from '@/utils/axios.js'
import { useSWRConfig } from 'swr'

const EMPTY_CONTENT = '<p><br></p>'

export default function WritePage() {
  const { postId } = useParams()
  const navigate = useNavigate()
  const [title, setTitle] = useState('')
  const editorRef = useRef(null)
  const { mutate } = useSWRConfig()
  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!title) {
      alert('제목을 입력해주세요.')
      return
    }

    const content = editorRef.current?.getInstance().getHTML()
    if (content === EMPTY_CONTENT) {
      alert('내용을 입력해주세요.')
      return
    }
    const data = { title, content }
    try {
      if (!postId) {
        const response = await axios.post('/api/posts', data)
        const postId = response.data.post_id
        navigate(`/posts/${postId}`)
      } else {
        await axios.patch(`/api/posts/${postId}`, data)
        await mutate(`/api/posts/${postId}`, null, { revalidate: true })
        navigate(`/posts/${postId}`)
      }
    } catch (e) {
      alert(e.response.data.message)
    }
  }

  const getPostData = async () => {
    const response = await axios.get(`/api/posts/${postId}`)
    const { post } = response.data
    setTitle(post.title)
    editorRef.current?.getInstance().setHTML(post.content)
  }
  useEffect(() => {
    if (postId && !title) {
      getPostData()
    }
  }, [postId])

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <form onSubmit={handleSubmit} className="space-y-6">
        <div>
          <input
            type="text"
            id="title"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
            className="mt-1 pl-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            placeholder="제목을 입력하세요"
          />
        </div>

        <div>
          <ToastUiEditor editorRef={editorRef} />
        </div>

        <div className="flex justify-end space-x-4">
          <Link
            to="/posts"
            className="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {' '}
            목록{' '}
          </Link>
          <button
            type="submit"
            className="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {postId ? '수정' : '작성'}
          </button>
        </div>
      </form>
    </div>
  )
}
