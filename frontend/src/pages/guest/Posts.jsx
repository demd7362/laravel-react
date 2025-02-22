import { axios } from '@/utils/axios.js'
import { Link, useSearchParams } from 'react-router-dom'
import useSWR from 'swr'
import Post from '@/components/Post.jsx'

const fetcher = (url) => axios.get(url).then((res) => res.data)
export default function Posts() {
  const [searchParams, setSearchParams] = useSearchParams()
  const pageNumber = searchParams.get('pageNumber') || 1
  const url = `/api/posts?pageNumber=${pageNumber}`
  const { data, error, isLoading } = useSWR(url, fetcher)
  if (isLoading) {
    return <span>로딩중</span>
  }
  const { posts } = data
  return (
    <>
      <div>
        {posts.data.map((post) => (
          <Post key={post.id} post={post} />
        ))}
      </div>
      <div className="py-12 px-4 flex gap-2 justify-center">
        {posts.links.map((link, index) =>
          link.url ? (
            <Link
              className={`p-1 mx-1 ${link.active ? 'font-bold' : ''}`}
              dangerouslySetInnerHTML={{ __html: link.label }}
              key={index}
              to={`/posts?pageNumber=${link.label}`}
            />
          ) : (
            <span
              key={index}
              dangerouslySetInnerHTML={{ __html: link.label }}
              className={`p-1 mx-1 text-slate-300`}
            ></span>
          ),
        )}
      </div>
    </>
  )
}
