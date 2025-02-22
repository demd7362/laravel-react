import { useRef, useState } from 'react'
import { axios } from '@/utils/axios.js'
import { useLocation, useNavigate } from 'react-router-dom'
import useRefresh from '@/hooks/useRefresh.jsx'

export default function CommentInput({ postId, mutate }) {
  const [comment, setComment] = useState('')
  const textareaRef = useRef(null)
  const handleSubmit = async () => {
    try {
      await axios.post(`/api/posts/${postId}/comments`, {
        content: comment,
      })
      mutate(`/api/posts/${postId}`) // 내가 댓글을 쓴 사이 다른 사람이 댓글을 작성했을 수도 있으니 revalidate
      setComment('')
    } catch (e) {
      alert(e.response.data.message)
      throw e
    }
  }

  const handleChange = (e) => {
    const value = e.target.value
    setComment(value)
  }

  return (
    <div className="mt-12 max-w-4xl mx-auto">
      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="relative">
          <textarea
            ref={textareaRef}
            className="w-full p-4 border-b border-gray-200 resize-none focus:outline-none focus:ring-2 focus:ring-indigo-200 transition duration-200"
            value={comment}
            onChange={handleChange}
            placeholder="댓글을 입력하세요..."
            rows={4}
          />
        </div>
        <div className="px-4 py-3 bg-gray-50 text-right">
          <button
            className="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
            onClick={handleSubmit}
          >
            작성
          </button>
        </div>
      </div>
    </div>
  )
}
