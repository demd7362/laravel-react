import { useLocation, useNavigate } from 'react-router-dom'
import useRefresh from '@/hooks/useRefresh.jsx'
import { Pencil, Trash2 } from 'lucide-react'
import { axios } from '@/utils/axios.js'

export default function CommentButtonWrapper({ commentId, postId }) {
  const handleModifyComment = async () => {
    const newComment = prompt('댓글을 입력해주세요.')
    try {
      const response = await axios.patch(
        `/api/posts/${postId}/comments/${commentId}`,
        { content: newComment },
      )
      alert(response.data.message)
    } catch (e) {
      alert(e.response.data.message)
      throw e
    }
  }

  const handleDelete = async () => {
    try {
      const response = await axios.delete(
        `/api/posts/${postId}/comments/${commentId}`,
      )
      alert(response.data.message)
    } catch (e) {
      alert(e.response.data.message)
      throw e
    }
  }

  return (
    <div className="flex space-x-2">
      <button
        onClick={handleModifyComment}
        className="text-blue-500 hover:text-blue-700"
      >
        <Pencil size={18} />
      </button>
      <button
        onClick={handleDelete}
        className="text-red-500 hover:text-red-700"
      >
        <Trash2 size={18} />
      </button>
    </div>
  )
}
