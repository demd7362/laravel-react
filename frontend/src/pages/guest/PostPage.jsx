import { useParams } from 'react-router-dom'
import ToastUiViewer from '@/components/ToastUiViewer.jsx'
import Comments from '@/components/Comments.jsx'
import PostButtonWrapper from '@/components/PostButtonWrapper.jsx'
import useSWR from 'swr'
import { fetcher } from '@/App.jsx'
import { formatDate } from '@/utils/date.js'

export default function PostPage() {
  const { postId } = useParams()
  const { data, error } = useSWR(`/api/posts/${postId}`, fetcher)
  const { post } = data ?? {}
  if (!post) {
    return null
  }
  return (
    <div className="container mx-auto px-4 py-4 border mt-3">
      <h1 className="text-3xl font-bold mb-4">{post.title}</h1>
      <div className="mb-4 text-gray-600">
        <span>작성자: {post.user.nickname}</span>
        <span className="ml-4">작성일자: {formatDate(post.created_at)}</span>
      </div>
      <ToastUiViewer content={post.content} /> <PostButtonWrapper />{' '}
      <Comments postId={postId} />
    </div>
  )
}
