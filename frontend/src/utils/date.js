export const formatDate = (str) => {
    const date = new Date(str)
    return `${date.getFullYear()}년 ${date.getMonth()}월 ${date.getDay()}일 ${date.getHours()}시 ${date.getMinutes()}분`
}
