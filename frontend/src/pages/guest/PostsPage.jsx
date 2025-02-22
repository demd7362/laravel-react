import { axios } from '@/utils/axios.js'
import { Link, useSearchParams } from 'react-router-dom'
import useSWR from 'swr'
import { formatDate } from '@/utils/date.js'
import { fetcher } from '@/App.jsx'
import Loading from '@/components/Loading.jsx'
import Paginate from '@/components/Paginate.jsx'

export default function PostsPage() {
  const [searchParams, setSearchParams] = useSearchParams()
  const page = searchParams.get('page') || 1
  const url = `/api/posts?page=${page}`
  const { data, error } = useSWR(url, fetcher)

  if (error) {
    return <div className="text-center text-red-500">오류가 발생했습니다.</div>
  }

  const { posts } = data ?? {}
  const postLinkProvider = (link) => {
    return `/posts?page=${link.label}`
  }
  return (
    <main className="max-w-4xl mx-auto py-8">
      <div className="bg-white shadow-md rounded-lg overflow-hidden">
        {posts &&
          posts.data.map((post) => (
            <Link
              key={post.id}
              to={`/posts/${post.id}`}
              className="block p-6 border-b border-gray-200 hover:bg-gray-50 transition duration-150"
            >
              <div className="flex justify-between items-center">
                <p className="text-lg font-semibold text-gray-800 truncate">
                  {post.title}
                </p>
                <span className="text-sm text-gray-500">
                  {formatDate(post.created_at)}
                </span>
              </div>
            </Link>
          ))}
      </div>

      <div className="mt-8 flex items-center justify-between">
        <div className="flex gap-2">
          {posts && (
            <Paginate links={posts.links} linkProvider={postLinkProvider} />
          )}
        </div>
        {posts && (
          <Link
            to="/posts/write"
            className="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
          >
            {' '}
            글 작성{' '}
          </Link>
        )}
      </div>
    </main>
  )
}
