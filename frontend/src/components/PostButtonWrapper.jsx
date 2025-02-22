import { useNavigate, useParams } from 'react-router-dom'
import { Edit, List, ThumbsDown, ThumbsUp, Trash2 } from 'lucide-react'
import { axios } from '@/utils/axios.js'

export default function PostButtonWrapper() {
  const { postId } = useParams()
  const navigate = useNavigate()
  const handleDelete = async () => {
      try {
          await axios.delete(`/api/posts/${postId}`)
          alert('삭제되었습니다.')
          navigate('/posts', {replace: true})
      } catch (e){
          alert(e.response.data.message)
      }
  }
  const handleEdit = () => {
      navigate(`/posts/write/${postId}`)
  }

  return (
    <div className="flex justify-end gap-4 mt-8 mb-3">
      <button
        onClick={handleEdit}
        className="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors shadow-lg hover:shadow-xl"
      >
        <Edit size={24} />
      </button>
      <button
        onClick={handleDelete}
        className="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors shadow-lg hover:shadow-xl"
      >
        <Trash2 size={24} />
      </button>
      <button
        onClick={() => navigate('/posts')}
        className="p-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors shadow-lg hover:shadow-xl"
      >
        <List size={24} />
      </button>
    </div>
  )
}
