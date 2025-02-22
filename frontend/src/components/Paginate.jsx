import { Link } from 'react-router-dom'

export default function Paginate({links, linkProvider}) {
    return (
        <>
            {links.map((link, index) =>
                link.url ? (
                    <Link
                        key={index}
                        to={linkProvider(link)}
                        className={`px-3 py-1 rounded-md text-sm ${
                            link.active
                                ? 'bg-indigo-600 text-white font-bold'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        } transition duration-200`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ) : (
                    <span
                        key={index}
                        className="px-3 py-1 text-sm text-gray-400 cursor-not-allowed"
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ),
            )}
        </>
    )
}
