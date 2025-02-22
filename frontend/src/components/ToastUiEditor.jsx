import { useEffect } from 'react'
import { Editor } from '@toast-ui/react-editor'
import '@toast-ui/editor/dist/toastui-editor.css'

export default function ToastUiEditor({ content, editorRef }) {
  const addImageBlobHook = async (file, callback) => {
    // const formData = new FormData()
    // formData.append('file', file)
    // const uploadPath = await uploadFile(formData)
    // const instance = editorRef?.current?.getInstance()
    // instance.setHTML(instance?.getHTML() + `<img src="${uploadPath}" alt=""/>`)
    // // 이미지 업로드창 닫기
    // instance.eventEmitter.emit('closePopup')
  }
  return (
    <Editor
      initialValue={content ?? ' '}
      initialEditType="wysiwyg"
      autofocus={false}
      ref={editorRef}
      toolbarItems={[
        ['heading', 'bold', 'italic', 'strike'],
        ['hr', 'quote'],
        ['ul', 'ol', 'task'],
        ['table', 'image', 'link'],
        ['code', 'codeblock'],
      ]}
      hideModeSwitch={false}
      height="500px"
      language="ko-KR"
      hooks={{ addImageBlobHook }}
      customHTMLRenderer={{
        htmlBlock: {
          iframe(node) {
            return [
              {
                type: 'openTag',
                tagName: 'iframe',
                outerNewLine: true,
                attributes: node.attrs,
              },
              { type: 'html', content: node.childrenHTML },
              { type: 'closeTag', tagName: 'iframe', outerNewLine: false },
            ]
          },
        },
      }}
    />
  )
}
