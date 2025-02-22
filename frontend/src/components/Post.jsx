import { Link } from 'react-router-dom'

export default function Post({post}) {
    return (
        <Link to={`/posts/${post.id}`} className="p-4 border-b">
            <div>
                <span>작성일: </span>
                <span>{new Date(post.created_at).toLocaleTimeString()}</span>
            </div>
            <p className="font-medium">{post.body}</p>
        </Link>
    )
}
