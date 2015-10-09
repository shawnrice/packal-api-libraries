require_relative 'packal'

username = 'my_packal_username'
password = 'my_packal_password'


# Workflow Submission Example
#
#
#

file = '/path/to/my/workflow.alfredworkflow'
version = '1.0.0'

workfow = {
	:workflow => { :file => file, :version => version },
	:username => username,
	:password => password
}
output = Packal.queue(workflow.to_json)

begin
	JSON.parse(output)
rescue
	output
end

# Theme Submission Example
#
#

theme = {
	:theme => {
		:name => 'Example Theme',
		:tags => 'example,example theme,not really anything',
		:description => 'Just an __ugly__ example theme.',
		:uri => 'alfred://theme/searchForegroundColor=rgba(0,0,0,1.00)&resultSubtextFontSize=1&searchSelectionForegroundColor=rgba(0,0,0,1.00)&separatorColor=rgba(87,60,70,0.49)&resultSelectedBackgroundColor=rgba(0,0,0,0.06)&shortcutColor=rgba(179,179,179,1.00)&scrollbarColor=rgba(0,0,0,0.10)&imageStyle=0&resultSubtextFont=Geneva&background=rgba(147,255,255,0.98)&shortcutFontSize=2&searchFontSize=3&resultSubtextColor=rgba(208,153,153,1.00)&searchBackgroundColor=rgba(127,170,0,0.47)&name=Example%20Theme&resultTextFontSize=4&resultSelectedSubtextColor=rgba(224,57,65,1.00)&shortcutSelectedColor=rgba(166,166,166,1.00)&widthSize=4&border=rgba(0,166,0,1.00)&resultTextFont=Baskerville&resultTextColor=rgba(53,53,53,1.00)&cornerRoundness=0&searchFont=Helvetica%20Neue%20Light&searchPaddingSize=1&credits=Shawn%20Patrick%20Rice&searchSelectionBackgroundColor=rgba(178,215,255,1.00)&resultSelectedTextColor=rgba(0,0,0,1.00)&resultPaddingSize=3&shortcutFont=Menlo'
	},
	:username => username,
	:password => password
}
output = Packal.queue(theme.to_json)

begin
	JSON.parse(output)
rescue
	output
end


# Report Submission Example
#
#

report_type = 'Malicious Code'
report_workflow_revision_id = 589
report_message = 'This is a bad workflow that does __bad__ things to my computer. Here is a detailed explanation: ...'

report = {
	:report => {
		:type => report_type,
		:workflow_revision_id => report_workflow_revision_id,
		:message => report_message
	},
	:username => username,
	:password => password
}
output = Packal.queue(report.to_json)

begin
	JSON.parse(output)
rescue
	output
end