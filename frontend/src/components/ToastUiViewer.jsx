import { Viewer } from '@toast-ui/react-editor'

export default function ToastUiViewer({ content, style }) {
  return (
    <section className={style}>
      <Viewer
        initialValue={content}
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
                {
                  type: 'closeTag',
                  tagName: 'iframe',
                  outerNewLine: false,
                },
              ]
            },
          },
        }}
      />
    </section>
  )
}
