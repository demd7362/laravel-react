import FormInput from '@/components/FormInput.jsx'
import { Link, useNavigate } from 'react-router-dom'
import { useCallback, useState } from 'react'
import { useForm } from 'react-hook-form'
import { UserPlus } from 'lucide-react'
import { axios } from '@/utils/axios.js'

const converter = {
  email: '이메일',
  nickname: '닉네임',
}
export default function RegisterPage() {
  const {
    register,
    handleSubmit,
    formState: { errors },
    watch,
    setError,
    clearErrors,
  } = useForm()
  const navigate = useNavigate()
  const [isChecked, setIsChecked] = useState({
    nickname: 'pending',
    email: 'pending',
  })

  const signUp = async (data) => {
    try {
      const response = await axios.post('/api/register', data)
      localStorage.setItem('accessToken', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      navigate(`/login?email=${data.email}`)
    } catch (e) {
      alert(e.response.data.message)
      throw e
    }
  }

  const isDuplicated = async (field, value) => {
    const url = `/api/users/${field}/${value}/exists`
    try {
      const response = await axios.get(url)
      alert(response.data.message)
      return false
    } catch (e) {
      alert(e.response.data.message)
      return true
    }
  }

  const handleDuplicateCheck = useCallback(
    async (field) => {
      const value = watch(field)
      if (!value) {
        setError(field, {
          type: 'manual',
          message: `${converter[field]}을 입력해주세요.`,
        })
        return
      }
      try {
        const isDuplicate = await isDuplicated(field, value)
        if (isDuplicate) {
          setError(field, {
            type: 'manual',
            message: `이미 사용 중인 ${converter[field]}입니다.`,
          })
          setIsChecked((prev) => ({ ...prev, [field]: 'duplicate' }))
        } else {
          clearErrors(field)
          setIsChecked((prev) => ({ ...prev, [field]: 'checked' }))
        }
      } catch (error) {
        alert('중복 확인 중 오류가 발생했습니다.')
      }
    },
    [watch, setError, clearErrors, isDuplicated],
  )

  const handleInputChange = (field) => {
    setIsChecked((prev) => ({ ...prev, [field]: 'pending' }))
  }

  const onSubmit = useCallback(
    async (data) => {
      const notCheckedElements = Object.entries(isChecked).filter(
        ([k, v]) => v !== 'checked',
      )
      if (notCheckedElements.length) {
        const message =
          converter[notCheckedElements[0][0]] + ' 중복 확인 바랍니다.'
        alert(message)
        return
      }
      try {
        await signUp(data)
      } catch (error) {
        console.error('회원가입 실패:', error)
      }
    },
    [navigate, isChecked, signUp],
  )

  return (
    <div className="min-h-screen bg-gradient-to-br from-indigo-100 to-purple-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">
          회원가입{' '}
        </h2>
        <p className="mt-2 text-center text-sm text-gray-600">
          이미 계정이 있으신가요?{' '}
          <Link
            to={'/login'}
            className="font-medium text-indigo-600 hover:text-indigo-500 ml-1"
          >
            {' '}
            로그인{' '}
          </Link>
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="bg-white py-8 px-4 shadow-2xl sm:rounded-lg sm:px-10">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            <div>
              <FormInput
                id="nickname"
                label="닉네임"
                register={register}
                validation={{
                  required: { value: true, message: '닉네임을 입력해주세요.' },
                  minLength: {
                    value: 2,
                    message: '닉네임은 2자 이상이어야 합니다.',
                  },
                  maxLength: {
                    value: 16,
                    message: '닉네임은 16자 이하여야 합니다.',
                  },
                }}
                error={errors.nickname}
                onChange={() => handleInputChange('nickname')}
              />
              <button
                type="button"
                onClick={() => handleDuplicateCheck('nickname')}
                disabled={isChecked.nickname === 'checked'}
                className="mt-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {isChecked.nickname === 'checked' ? '확인 완료' : '중복 확인'}
              </button>
            </div>

            <div>
              <FormInput
                id="email"
                label="이메일"
                type="text"
                register={register}
                validation={{
                  required: { value: true, message: '이메일을 입력해주세요.' },
                  pattern: {
                    value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                    message: '유효한 이메일 주소를 입력해주세요.',
                  },
                }}
                error={errors.email}
                onChange={() => handleInputChange('email')}
              />
              <button
                type="button"
                onClick={() => handleDuplicateCheck('email')}
                disabled={isChecked.nickname === 'checked'}
                className="mt-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {isChecked.nickname === 'checked' ? '확인 완료' : '중복 확인'}
              </button>
            </div>

            <FormInput
              id="password"
              label="비밀번호"
              type="password"
              register={register}
              validation={{
                required: { value: true, message: '비밀번호를 입력해주세요.' },
                minLength: {
                  value: 6,
                  message: '비밀번호는 6자리 이상이어야 합니다.',
                },
                maxLength: {
                  value: 16,
                  message: '비밀번호는 16자리 이하여야 합니다.',
                },
              }}
              error={errors.password}
            />

            <FormInput
              id="confirmPassword"
              label="비밀번호 확인"
              type="password"
              register={register}
              validation={{
                required: {
                  value: true,
                  message: '비밀번호 확인을 입력해주세요.',
                },
                validate: (value) =>
                  value === watch('password') ||
                  '비밀번호가 일치하지 않습니다.',
              }}
              error={errors.confirmPassword}
            />

            <div>
              <button
                type="submit"
                className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
              >
                <UserPlus className="mr-2 h-5 w-5" /> 가입하기
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
