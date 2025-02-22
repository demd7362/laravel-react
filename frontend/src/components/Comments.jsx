import CommentButtonWrapper from '@/components/CommentButtonWrapper.jsx'
import { useSearchParams } from 'react-router-dom'
import useSWR from 'swr'
import { fetcher } from '@/App.jsx'
import Loading from '@/components/Loading.jsx'
import { formatDate } from '@/utils/date.js'
import Paginate from '@/components/Paginate.jsx'
import CommentInput from '@/components/CommentInput.jsx'

export default function Comments({ postId }) {
  const [searchParams, setSearchParams] = useSearchParams()
  const page = searchParams.get('page') || 1
  const { data, error, mutate } = useSWR(
    `/api/posts/${postId}/comments?page=${page}`,
    fetcher,
  )
  const { comments } = data ?? {}
  const commentLinkProvider = (link) => {
    return `/posts/${postId}?page=${link.label}`
  }
  return (
    <>
      <div>
        {comments &&
          comments.data.map((comment) => (
            <div
              key={comment.id}
              className="border-b py-2 flex justify-between items-start"
            >
              <div>
                <p className="font-bold">{comment.user.nickname}</p>
                <p>{comment.content}</p>
                <p className="text-sm text-gray-500">
                  {formatDate(comment.created_at)}
                </p>
              </div>
              <CommentButtonWrapper commentId={comment.id} postId={postId} />
            </div>
          ))}
      </div>
      <div className="mt-8 flex items-center justify-center">
        <div className="flex gap-2">
          {comments && (
            <Paginate
              links={comments.links}
              linkProvider={commentLinkProvider}
            />
          )}
        </div>
      </div>
      <CommentInput postId={postId} mutate={mutate} />
    </>
  )
}
