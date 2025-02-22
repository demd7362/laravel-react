import { useLocation, useNavigate } from 'react-router-dom'

export default function useRefresh(){
    const navigate = useNavigate()
    const location = useLocation()
    return () => {
        navigate(location.pathname, { replace: true})
    }
}
