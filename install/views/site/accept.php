<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-19 12:18
 */

use yii\helpers\Url;

$this->title = 'Feehi CMS' . Yii::t('install', 'license agreement');
?>
<div class="section">
    <div class="main">
        <pre class="agreement">Feehi CMS软件使用协议

版权所有 ©2015-<?= date('Y') ?>,Feehi CMS开源社区

感谢您选择Feehi CMS内容管理框架, 希望我们的产品能够帮您把网站发展的更快、更好、更强！

Feehi CMS遵循Apache2开源协议发布，并提供免费使用。

Feehi CMS建站系统由飞嗨(官网http://blog.feehi.com)发起并开源发布。
飞嗨包含以下网站：
飞嗨官网： http://blog.feehi.com
飞嗨分享：http://www.feehi.com
飞嗨短网址：http://d.feehi.com

Apache Licence是著名的非盈利开源组织Apache采用的协议。
该协议鼓励代码共享和尊重原作者的著作权，允许代码修改，再作为开源或商业软件发布。需要满足的条件：
1． 需要给代码的用户一份Apache Licence ；
2． 如果你修改了代码，需要在被修改的文件中说明；
3． 在延伸的代码中（修改和有源代码衍生的代码中）需要带有原来代码中的协议，商标，专利声明和其他原来作者规定需要包含的说明；
4． 如果再发布的产品中包含一个Notice文件，则在Notice文件中需要带有本协议内容。你可以在Notice中增加自己的许可，但不可以表现为对Apache Licence构成更改。

具体的协议参考：http://www.apache.org/licenses/LICENSE-2.0

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

Feehi CMS免责声明
  1、使用Feehi CMS构建的网站的任何信息内容以及导致的任何版权纠纷和法律争议及后果，Feehi CMS官方不承担任何责任。
  2、您一旦安装使用Feehi CMS，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。</pre>
    </div>
    <div class="bottom text-center">
        <a href="<?= Url::to(['check-environment']) ?>" class="btn btn-primary"><?= Yii::t('install', 'Accept') ?></a>
        <a href="<?= Url::to(['accept']) ?>" class="btn btn-primary"><?= Yii::t('install', 'Decline') ?></a>
    </div>
</div>
